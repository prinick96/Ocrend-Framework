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
  <div id=\"ocrend\">
    <div class=\"row\">
      <div class=\"col-sm-12\" style=\"text-align: center;\">
        <h2>";
        // line 6
        echo twig_escape_filter($this->env, twig_constant("APP"), "html", null, true);
        echo "</h2>
      </div>
      <div class=\"col-sm-12\">
        ";
        // line 9
        if ( !$this->getAttribute((isset($context["session"]) ? $context["session"] : null), "app_id", array(), "any", true, true)) {
            // line 10
            echo "          ";
            $this->loadTemplate("overall/login.twig", "index.twig", 10)->display($context);
            // line 11
            echo "          <hr />
          ";
            // line 12
            $this->loadTemplate("overall/register.twig", "index.twig", 12)->display($context);
            // line 13
            echo "        ";
        } else {
            // line 14
            echo "          <a href=\"?view=logout\" class=\"btn btn-primary\">Cerrar Sesión id : ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["session"]) ? $context["session"] : null), "app_id", array()), "html", null, true);
            echo "</a>
        ";
        }
        // line 16
        echo "      </div>
    </div>
  </div>
";
        // line 19
        $this->loadTemplate("overall/footer.twig", "index.twig", 19)->display($context);
        // line 20
        echo "</body>
</html>
";
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
        return array (  59 => 20,  57 => 19,  52 => 16,  46 => 14,  43 => 13,  41 => 12,  38 => 11,  35 => 10,  33 => 9,  27 => 6,  21 => 2,  19 => 1,);
    }
}
/* {% include 'overall/header.twig' %}*/
/* <body>*/
/*   <div id="ocrend">*/
/*     <div class="row">*/
/*       <div class="col-sm-12" style="text-align: center;">*/
/*         <h2>{{ constant('APP') }}</h2>*/
/*       </div>*/
/*       <div class="col-sm-12">*/
/*         {% if session.app_id is not defined %}*/
/*           {% include 'overall/login.twig' %}*/
/*           <hr />*/
/*           {% include 'overall/register.twig' %}*/
/*         {% else %}*/
/*           <a href="?view=logout" class="btn btn-primary">Cerrar Sesión id : {{ session.app_id }}</a>*/
/*         {% endif %}*/
/*       </div>*/
/*     </div>*/
/*   </div>*/
/* {% include 'overall/footer.twig' %}*/
/* </body>*/
/* </html>*/
/* */
