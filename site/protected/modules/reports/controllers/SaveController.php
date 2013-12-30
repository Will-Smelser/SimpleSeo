<?php

class SaveController extends ApiBaseController
{
	public function actionIndex(){
        require_once SEO_PATH_HELPERS . 'ApiResponse.php';

        //from ApiBaseController, if doesn't exist, then
        //a default error is created
        //$userId = $this->tokenUserId;
        $userId = Yii::app()->user->id;

        $errMsg = "Failed to save data.";

        $response = null;
        if(isset($_GET['type']) && $_GET['type'] === 'jsonp'){
            $response = (new \api\responses\ApiResponseJSONP());
        }else{
            $response = (new \api\responses\ApiResponseJSON());
        }

        $model=new Reportdata();
        if(isset($_POST['data']) && isset($_POST['uri']))
        {
            require_once(Yii::getPathOfAlias('application.components').'/Normalizer.php');

            //normalize the url
            $un = new URL\Normalizer();
            $un->setUrl($_POST['uri']);
            $_POST['uri'] = $un->normalize();

            $model->data=$_POST['data'];
            $model->user_id = $userId;
            $model->domain = $un->parseDomain(parse_url($_POST['uri'],PHP_URL_HOST));
            $model->uri = $_POST['uri'];

            try{
                if($model->save()){
                    $response->success("Data Saved!",null);
                    echo $response->doPrint();

                    Yii::app()->end();
                }else{
                    //var_dump($model);
                    foreach($model->getErrors() as $err){

                        $errMsg .= "ERROR:  {$err[0]}";
                    }
                }
            }catch(Exception $e){
                $errMsg .= 'ERROR: ' . $e->getMessage();
            }
        }else{
            $errMsg .= '  Expected post data with variables: data, uri.';
        }

        $code = \api\responses\ApiCodes::$badRequest;
        $response->failure($errMsg,$code);
        echo $response->doPrint();

        Yii::app()->end();
    }

    /**
     * Override this from ApiBaseController
     * @param $action
     * @return bool|void
     */
    public function beforeAction($action){
        return true;
    }
}