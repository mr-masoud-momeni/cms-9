<?php
function menu_navigation($array) {
    foreach ($array as $value) {
        if (isset($value['children'])){
            echo "<li class=\"dropdown\"><a  href=\"".$value['href']."\"><i class=\"".$value['icon']."\"></i>".$value['text']."<i class=\"bi bi-chevron-down\"></i></a>";
            echo "<ul>";
            menu_navigation($value['children']);
            echo "</ul></li>";
        } else {
            echo "<li><a class=\"nav-link scrollto\" href=\"".$value['href']."\"><i class=\"".$value['icon']."\"></i>".$value['text']."</a>";
        }
    }
}