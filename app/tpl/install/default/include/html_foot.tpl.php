    <!--表单验证 js-->
    <script src="{:DIR_STATIC}lib/baigoValidate/3.0.0/baigoValidate.min.js" type="text/javascript"></script>

    <!--表单 ajax 提交 js-->
    <script src="{:DIR_STATIC}lib/baigoSubmit/2.1.0/baigoSubmit.min.js" type="text/javascript"></script>

    <script type="text/javascript">
    $(document).ready(function(){
        $('#loading_mask').fadeOut();
    });
    </script>

    <!--bootstrap-->
    <script src="{:DIR_STATIC}lib/bootstrap/4.3.1/js/bootstrap.bundle.min.js" type="text/javascript"></script>

    <!--
        Powered by
        <?php if ($config['tpl']['path'] == 'default') {
            echo $config['version']['prd_sso_name'], ' ';
        } else {
            echo $config['tpl']['path'], ' SSO ';
        }
        echo $config['version']['prd_sso_ver']; ?>
    -->

</body>
</html>