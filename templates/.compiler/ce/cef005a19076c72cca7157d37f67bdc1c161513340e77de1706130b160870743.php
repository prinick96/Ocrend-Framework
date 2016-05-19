<?php

/* index.twig */
class __TwigTemplate_895db3c0268efbc12622b7e844476346ab449dc1d7f1b749d9400ecdaad44efe extends Twig_Template
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
        $this->loadTemplate("overall/header.twig", "index.twig", 1)->display($context);
        // line 2
        echo "<body>
  <h1>OCREND SOFTWARE</h1>
";
        // line 4
        $this->loadTemplate("overall/footer.twig", "index.twig", 4)->display($context);
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  25 => 4,  21 => 2,  19 => 1,);
    }
}
/* {% include 'overall/header.twig' %}*/
/* <body>*/
/*   <h1>OCREND SOFTWARE</h1>*/
/* {% include 'overall/footer.twig' %}*/
/* */
