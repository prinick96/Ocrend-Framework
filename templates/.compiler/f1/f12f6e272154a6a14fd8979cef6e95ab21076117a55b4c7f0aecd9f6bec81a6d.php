<?php

/* overall/footer.twig */
class __TwigTemplate_35066cded854603663fddfe26af4ec1addb5ec3cc66f7fbff258051b880d99b6 extends Twig_Template
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
        echo "<script src=\"views/app/js/jquery.js\"></script>
<script src=\"views/app/js/jquery.numeric.js\"></script>
<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js\" integrity=\"sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS\" crossorigin=\"anonymous\"></script>
";
        // line 4
        if ( !$this->getAttribute((isset($context["session"]) ? $context["session"] : null), "app_id", array(), "any", true, true)) {
            // line 5
            echo "<script src=\"views/app/js/login.js\"></script>
<script src=\"views/app/js/register.js\"></script>
";
        }
        // line 8
        echo "<script>
  \$('.numeric').numeric();
</script>
";
    }

    public function getTemplateName()
    {
        return "overall/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 8,  26 => 5,  24 => 4,  19 => 1,);
    }
}
/* <script src="views/app/js/jquery.js"></script>*/
/* <script src="views/app/js/jquery.numeric.js"></script>*/
/* <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>*/
/* {% if session.app_id is not defined %}*/
/* <script src="views/app/js/login.js"></script>*/
/* <script src="views/app/js/register.js"></script>*/
/* {% endif %}*/
/* <script>*/
/*   $('.numeric').numeric();*/
/* </script>*/
/* */
