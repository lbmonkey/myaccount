<?php
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', true);
/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Application/' );
define ( 'BIND_MODULE', 'Admin' );
require './ThinkPHP/ThinkPHP.php';