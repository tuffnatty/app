<?php
/*
 * Emoticons Extension setup file
 */
$app = F::app();
$dir = dirname(__FILE__) . '/';

/*
 * classes
 */
$app->registerClass('Emo', $dir . 'Emo.class.php');

/*
 * hooks
 */
$app->registerHook('ParserFirstCallInit', 'Emo', 'onParserFirstCallInit');
