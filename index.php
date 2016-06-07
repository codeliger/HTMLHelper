<?php
/**
 * Created by PhpStorm.
 * User: Benjamin Tiessen
 * Date: 2016-06-07
 * Time: 10:00 AM
 */

require("HTMLHelper.php");

$document = new Document("My First Webpage");

$document->head()->addChild(new ElementFull("title"));
$document->head()->last()->addChild(new Text("Web Page"));


$document->head()->addChild(new Meta("charset", "utf-8"));

$document->body()->addChild(new Header(1, "Ben's Website"));

echo $document;
