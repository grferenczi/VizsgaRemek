<?php
function MissingPage(){
    $str = <<<EOT
    <div class="content-width text-center mt-5"><h5>Oldal nem található!</h5></div>
    EOT;
    return $str;
}