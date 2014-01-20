<?php

class ReportsController extends RController
{

	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';

    public function filters()
    {
        return array('rights');
    }

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id,$template='index')
	{
        $this->includeProtectedController();
        $templates = ProtectedController::getTemplates();

        if(!isset($templates[$template]))
            throw new CHttpException(404,'Requested template does not exist.');

        Yii::app()->theme = $templates[$template]['theme'];
        $model = $this->loadModel($id);
		$this->render("reports.views.protected.$template",array(
			'model'=>$model,
            'data'=>$model->data,
            'target'=>$model->uri
		));
	}

    private function includeProtectedController(){
        require_once Yii::getPathOfAlias('application.modules.reports.controllers.ProtectedController') . '.php';
    }

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
        $userid = intval(Yii::app()->user->id);

		$model = $this->loadModel($id);
        if($model->user_id == $userid)
            $model->delete();
        else{
            http_response_code(401);
            echo "You do not have permission";
            Yii::app()->end();
        }

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        $this->includeProtectedController();
        $templates = ProtectedController::getTemplates();

        $userid = intval(Yii::app()->user->id);

        $model = new Reportdata('search');
        $model->unsetAttributes();
        if(isset($_GET['Reportdata']))
            $model->attributes = $_GET['Reportdata'];

        $this->render('index',array('model'=>$model,'userid'=>$userid,'templates'=>$templates));

	}

    public function actionCrawler(){
        $this->render('crawler');
    }

    public function actionDocrawl($url,$maxLinks,$depth,$nofollow=false){
        $this->layout = 'application.views.layouts.empty';

        require_once(Yii::getPathOfAlias('ext.seo').'/config.php');
        require_once SEO_PATH_CLASS . 'Crawler.php';
        require_once SEO_PATH_HELPERS . 'ClientHash.php';

        //get a token
        $token = "TOKEN_GET_FAILED";
        try{
            $token = \api\clients\ClientHash::getToken(Yii::app()->params['apiKeySample'],'sample',SEO_HOST);
        }catch(Exception $e){
            //do nothing, just everything will fail.
        }




        $nofollow = ($nofollow === 'true') ? true : false;

        $crawler = new Crawler($url,SEO_URI_HELPERS.'PageLoadLinks.php',$token,$nofollow,$maxLinks,$depth);
        $result = $crawler->start();

        echo json_encode($result);
        Yii::app()->end();
    }

    public function actionProcessReport($url){

        $this->layout = 'application.views.layouts.empty';

        require_once Yii::getPathOfAlias('ext.seo').'/config.php';
        require_once SEO_PATH_HELPERS . 'ClientHash.php';
        require_once Yii::getPathOfAlias('ext.seo.apiuser.class') . '/PageLoad.php';
        require_once Yii::getPathOfAlias('ext.seo.apiuser') . '/SeoApi.php';

        $result = null;
        try{
            $nonce = \api\clients\ClientHash::nonce();
            $hash = \api\clients\ClientHash::hash($nonce,Yii::app()->params['apiKeySample']);
            $token = \api\clients\ClientHash::makeRequest('sample', $nonce, $hash, SEO_HOST, '/user/reports/crawler');

            $config = include Yii::getPathOfAlias('ext.seo.apiuser').'/config.php';

            $thread = new simple_seo_api\PageLoad();
            $seo = new simple_seo_api\SeoApi($config, $thread, $token);

            $seo->addMethods('Body','all');
            $seo->addMethods('Server','all');
            $seo->addMethods('Head','all');
            $seo->addMethods('Social','all');
            $seo->addMethods('Google','all');
            $seo->addMethods('W3c','all');
            $seo->addMethods('Moz','all');
            $seo->addMethods('Semrush','all');

            $result = $seo->exec($url);

        }catch(Exception $e){
            echo '{"result":false}';
            Yii::app()->end();
        }

        if(empty($result)){
            echo '{"result":false}';
            Yii::app()->end();
        }

        $userid = Yii::app()->user->id;

        //save the result to db
        $model=new Reportdata();
        $model->saveData($userid,$url,$model->apiuserData2JSON($result));

        $creditType = Apicredits::$typeReport;

        if(Apicredits::hasCredit($userid,$creditType)){
            Apicredits::useCredit($userid, $creditType);

            //record stats
            $stats = new Reportstats();
            $stats->user = $userid;
            $stats->type = 'crawler';
            $stats->request = $url;
            $stats->save();

            echo '{"result":true}';
        }else{
            echo '{"result":false}';
        }
        Yii::app()->end();
    }


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Reportdata the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Reportdata::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

}
