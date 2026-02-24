<?php

$ruleset = new TwigCsFixer\Ruleset\Ruleset();

$ruleset->addStandard(new TwigCsFixer\Standard\Symfony());
$config = new TwigCsFixer\Config\Config();
$config->setRuleset($ruleset);
$finder = $config->getFinder();
$finder->in(__DIR__.'/templates');

return $config;
