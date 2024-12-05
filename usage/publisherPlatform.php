<?php

if (isset($_GET['publisherPlatformID'])){
	$publisherPlatformID = $_GET['publisherPlatformID'];
	$platformID = '';
}

if (isset($_GET['platformID'])){
	$platformID = $_GET['platformID'];
	$publisherPlatformID = '';
}



if (($publisherPlatformID == '') && ($platformID == '')){
	header( 'Location: publisherPlatformList.php?error=1' ) ;
}


$pageTitle = _('View or Edit Publisher / Platform');

include 'templates/header.php';


if ($publisherPlatformID) {
	$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
	$pub = new Publisher(new NamedArguments(array('primaryKey' => $obj->publisherID)));
	$deleteParam = "publisherPlatformID=$publisherPlatformID";
	$deleteText = _('Delete Publisher');
	$displayName = $pub->name;
}else if ($platformID){
	$obj = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
  $deleteParam = "platformID=$platformID";
  $deleteText = _('Delete Platform');
	$displayName = $obj->name;
}
?>

<!-- TODO: check this layout -->
<main id="main-content">
	<nav aria-label="<?php echo _("Publisher / Platform Tools"); ?>" class="sidemenu">
		<ul class="nav side">
			<?php echo usage_sidemenu(watchString($_GET['showTab'])); ?>
		</ul>
	</nav>
	<article>
    <h2><?php echo $displayName; ?></h2>
		<span class="editElement">
			<?php if ($platformID): ?>
				<button type="button" onclick='myDialog("ajax_forms.php?action=getUpdatePlatformForm&platformID=<?php echo $platformID; ?>&height=530&width=518&modal=true",530,520)' class='thickbox link'>
					<i class="fa fa-pencil" aria-hidden="true"></i>
				</button>
			<?php endif; ?>

			<a href="deletePublisherPlatformConfirmation.php?<?php echo $deleteParam; ?>"><?php echo $deleteText; ?></a>
		</span>

		<input type='hidden' name='platformID' id='platformID' value='<?php echo $platformID; ?>'>
		<input type='hidden' name='publisherPlatformID' id='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>

		<?php foreach ($links as $key => $value) { ?>
				<div id ='div_<?php echo $key ?>' class="usage_tab_content">
					
					<div class='mainContent'>
						<div class='div_mainContent'>
						</div>
					</div>
				</div>

		<?php } ?>
</article>
</main>

<?php
include 'templates/footer.php';
?>
<script type="text/javascript" src="js/publisherPlatform.js"></script>
    <script>
      $(document).ready(function() {
		<?php if ((isset($_GET['showTab'])) && ($_GET['showTab'] == 'sushi')){ ?>
        	$('a.showSushi').click();
        <?php }else{ ?>
        	$('a.showImports').click().css("font-weight", "bold");
        <?php } ?>
      });
    </script>

</body>
</html>