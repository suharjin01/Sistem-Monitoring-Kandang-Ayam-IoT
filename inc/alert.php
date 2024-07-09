<?php

function alertSuccess($message){
    echo '
    <div class="alert alert-info alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-check"></i> Berhasil!</h5>'
      . $message .
    '</div>
    ';
}

?>