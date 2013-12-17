<?php
if($data->error !== false){
    echo $this->renderPartial('/partials/error',array('info'=>$data));
}elseif(count($data->data) == 0){
    echo $this->renderPartial('/partials/errorTopLevel');
}else{
    foreach($data->data as $key=>$val)
        echo '<li>' . ucwords(str_replace('_',' ',$key)) . ': ' . Yii::app()->controller->renderInput($val).'</li>';
}
?>
