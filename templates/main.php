<?php
/**
 * tobiju
 *
 * @link      https://github.com/tobiju/bookdown-bootswatch-templates for the canonical source repository
 * @copyright Copyright (c) 2015 Tobias Jüschke
 * @license   https://github.com/tobiju/bookdown-bootswatch-templates/blob/master/LICENSE.txt New BSD License
 */
require_once __DIR__ . '/helper/tocList.php';


$config = $this->page->getRoot()->getConfig();
$templatePath = __DIR__;

// register view helper
$helpers = $this->getHelpers();

$helpers->set('tocListHelper', function () use ($config) {
    return new \tocListHelper($this->get('anchorRaw'), $config);
});

// register the templates
$templates = $this->getViewRegistry();

$templates->set('head', $templatePath . '/head.php');
$templates->set('meta', $templatePath . '/meta.php');
$templates->set('style', $templatePath . '/style.php');
$templates->set('body', $templatePath . '/body.php');
$templates->set('script', $templatePath . '/script.php');
$templates->set('nav', $templatePath . '/nav.php');
$templates->set('core', $templatePath . '/core.php');
$templates->set('navheader', $templatePath . '/navheader.php');
$templates->set('navfooter', $templatePath . '/navfooter.php');
$templates->set('toc', $templatePath . '/toc.php');
$templates->set('partialTopNav', $templatePath . '/partial/topNav.php');
$templates->set('partialBreadcrumb', $templatePath . '/partial/breadcrumb.php');
$templates->set('partialSideNav', $templatePath . '/partial/sideNav.php');
?>

<!DOCTYPE html>
<html>
<?= $this->render("head"); ?>
<?= $this->render("body"); ?>
</html>
