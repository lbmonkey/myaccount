<?php

function is_login(){
    if(session('admin_user')){
        return true;
    }else{
        return false;
    }
}