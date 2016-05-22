<?php

/* overall/login.twig */
class __TwigTemplate_3c66b6ce43bb0bb2e1d64204bf1a23e12fc737eb6d2dbe66978ff15835f21aaf extends Twig_Template
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
        echo "<form role=\"form\" enctype=\"application/x-www-form-urlencoded\" id=\"login_form\">

  <div class=\"alert hide\" id=\"ajax_login\"></div>

  <div class=\"form-group\">
    <label>Usuario:</label>
    <input type=\"text\" name=\"user\" class=\"form-control\" required=\"\" />
  </div>
  <div class=\"form-group\">
    <label>Contraseña:</label>
    <input type=\"password\" name=\"pass\" class=\"form-control\" required=\"\" />
  </div>
  <div class=\"form-group\">
    <button type=\"button\" id=\"login\" class=\"btn btn-success\">Iniciar Sesión</button>
  </div>
</form>
";
    }

    public function getTemplateName()
    {
        return "overall/login.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
/* <form role="form" enctype="application/x-www-form-urlencoded" id="login_form">*/
/* */
/*   <div class="alert hide" id="ajax_login"></div>*/
/* */
/*   <div class="form-group">*/
/*     <label>Usuario:</label>*/
/*     <input type="text" name="user" class="form-control" required="" />*/
/*   </div>*/
/*   <div class="form-group">*/
/*     <label>Contraseña:</label>*/
/*     <input type="password" name="pass" class="form-control" required="" />*/
/*   </div>*/
/*   <div class="form-group">*/
/*     <button type="button" id="login" class="btn btn-success">Iniciar Sesión</button>*/
/*   </div>*/
/* </form>*/
/* */
