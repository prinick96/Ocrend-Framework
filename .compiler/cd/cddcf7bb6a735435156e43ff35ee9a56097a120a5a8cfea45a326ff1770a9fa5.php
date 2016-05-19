<?php

/* error.twig */
class __TwigTemplate_c883f7766fb88a42e9229026371b4e73cf1a940466087d4415be16432f355e38 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->loadTemplate("overall/header.twig", "error.twig", 1)->display($context);
        // line 2
        echo "<body>
  Controlador ";
        // line 3
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["get"]) ? $context["get"] : null), "view", array()), "html", null, true);
        echo " no encontrado
";
        // line 4
        $this->loadTemplate("overall/footer.twig", "error.twig", 4)->display($context);
    }

    public function getTemplateName()
    {
        return "error.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  28 => 4,  24 => 3,  21 => 2,  19 => 1,);
    }
}
/* {% include 'overall/header.twig' %}*/
/* <body>*/
/*   Controlador {{ get.view }} no encontrado*/
/* {% include 'overall/footer.twig' %}*/
/* */
