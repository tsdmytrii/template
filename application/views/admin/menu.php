<?php include(APPPATH . 'language/'.$this->session->userdata('lang_iso').'/inter_lang.php');?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="margin-bottom: 25px;">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Template</a>
    </div>

    <div class="collapse navbar-collapse navbar-ex1-collapse pull-left">
        <ul class="nav navbar-nav">


            <?php foreach ($menu as $key => $value): ?>
                <?php if ($value['parent_id'] == 0): ?>

                    <li class="<?php if (uri_string() == $value['url']) echo ' active'; if ($value['child']) echo ' dropdown';?>">

                        <?php if ($value['url'] == ''): ?>
                            <a class="menuItem<?php if ($value['child']) echo ' dropdown-toggle';?>"><?= $value['lang']['name']; ?><?php if ($value['child']) echo '<b class="caret"></b>';?></a>
                        <?php else: ?>
                            <a class="new_component<?php if ($value['child']) echo ' dropdown-toggle';?>" href="<?= base_url() ?><?= $value['url']; ?>" title="<?= $value['lang']['name']; ?>"><?= $value['lang']['name']; ?><?php if ($value['child']) echo '<b class="caret"></b>';?></a>
                        <?php endif; ?>

                        <ul class="dropdown-menu">

                            <?php foreach ($menu as $k => $val): ?>

                                <?php if ($val['parent_id'] == $value['id']): ?>
                                    <?php if ($val['url'] == ''): ?>
                                        <li><a href="#"><?= $val['lang']['name']; ?></a></li>
                                    <?php else: ?>
                                        <li><a class="new_component subMenuItem" href="<?= base_url() ?><?= $val['url'] ?>" title="<?= $val['lang']['name']; ?>"><?= $val['lang']['name']; ?></a></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>

                        </ul>

                    </li>

                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>

    <ul class="nav navbar-nav navbar-right">
        <li><a target="_blank" href="<?php echo base_url();?>"><?php echo $localization['main_page'];?></a></li>
        <li class="dropdown">
            <a class="dropdown-toggle" style="padding: 7px 15px;">
                <div class="btn btn-default">
                    <span class="glyphicon glyphicon-user"></span>
                    <?= $user['result']['first_name'] ?> <?= $user['result']['last_name'] ?>
                </div>
            </a>
            <ul class="dropdown-menu">
                <li><a href="<?= base_url() ?>admin/login/logout"><span class="glyphicon glyphicon-log-out"></span>&nbsp;<?php echo $localization['log_out'];?></a></li>
            </ul>
        </li>
    </ul>
</nav>
<div class="navbar" style="margin-bottom: 25px;">
	<div class="navbar-inner">
		<div class="container">
			<a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
                        <a href="#" class="brand" style="color: #fff;">Template admin</a>
			<div class="nav-collapse" id="nav">
				<ul class="nav">



				</ul>
			</div><!-- /.nav-collapse -->
		</div>
	</div><!-- /navbar-inner -->
</div>