<?php include($cfg['pathInclude'] . 'html_head' . GK_EXT_TPL); ?>

    <div class="container">
        <div class="bg-card-md my-5">
            <div class="clearfix mb-2">
                <div class="float-left">
                    <img class="img-fluid bg-head-logo" src="{:DIR_STATIC}sso/console/<?php echo $config['tpl']['path']; ?>/image/logo_green.svg">
                </div>
                <h3 class="float-right"><?php echo $lang->get('Upgrader'); ?></h3>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <ul class="nav flex-column mb-3">
                                <?php foreach ($config['install']['upgrade'] as $key_opt=>$value_opt) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link<?php if ($cfg['active'] == $key_opt) { ?> disabled<?php } ?>" href="<?php echo $route_install; ?>upgrade/<?php echo $key_opt; ?>/">
                                            <?php echo $lang->get($value_opt); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="col-lg-8">
                            <h4><?php echo $cfg['sub_title']; ?></h4>
                            <hr>

                            <div class="alert alert-warning">
                                <span class="fas fa-exclamation-triangle"></span>
                                <?php echo $lang->get('Upgrading'); ?>
                                <span class="badge badge-warning"><?php echo $installed['prd_installed_ver']; ?></span>
                                <?php echo $lang->get('To'); ?>
                                <span class="badge badge-warning"><?php echo $config['version']['prd_sso_ver']; ?></span>
                            </div>
