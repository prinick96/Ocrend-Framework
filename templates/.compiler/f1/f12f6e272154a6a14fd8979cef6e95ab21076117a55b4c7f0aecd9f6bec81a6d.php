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
<script>
  \$.ajax({
    url : 'api/login',
    type : 'GET',
    data : {
      user : 'prinick',
      pass : '123456'
    },
    success : function(json) {
      window.alert(json);
    },
    error : function(xhr, status) {
      window.alert('ERROR');
    }
  });
</script>
</body>
</html>
";
    }

    public function getTemplateName()
    {
        return "overall/footer.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
/* <script src="views/app/js/jquery.js"></script>*/
/* <script>*/
/*   $.ajax({*/
/*     url : 'api/login',*/
/*     type : 'GET',*/
/*     data : {*/
/*       user : 'prinick',*/
/*       pass : '123456'*/
/*     },*/
/*     success : function(json) {*/
/*       window.alert(json);*/
/*     },*/
/*     error : function(xhr, status) {*/
/*       window.alert('ERROR');*/
/*     }*/
/*   });*/
/* </script>*/
/* </body>*/
/* </html>*/
/* */
