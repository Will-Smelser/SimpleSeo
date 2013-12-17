<?php

if(($info = $this->parse($data)) !== false){
    //&& $info->error === false){

    echo "<ul>\n";
    foreach($methods as $m){
        if(isset($info->data->$m) && $info->data->$m->error === false)
            echo $this->renderPartial('/partials/server/methods/'.$m,array('data'=>$info->data->$m));
        else
            echo $this->renderPartial('/partials/error',array('info'=>$info->data->$m));

    }
    echo "</ul>\n";

}else{
    echo '<li>'.$this->renderPartials('/partials/errorToplevel').'</li>';
}
?>