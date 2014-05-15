<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/feed.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs">
        <a href="#tab-general"><?php echo $tab_general; ?></a>
      </div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      	<div id="tab-general">
          <table class="form">
            <tr>
              <td><?php echo $entry_status; ?></td>
              <td>
                <select name="oc_api_status">
                  <?php if ($oc_api_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </td>
            </tr>
            <tr>
             <td>
          	    <label for="entry_key"><?php echo $entry_appid; ?></label>
              </td>
              <td>
                <input type="text" name="oc_api_appid" value="<?php echo $oc_api_appid; ?>" />
              </td>
            </tr>
            <tr>
             <td>
          	    <label for="entry_key"><?php echo $entry_pubkey; ?></label>
              </td>
              <td>
                <input type="text" name="oc_api_pubkey" value="<?php echo $oc_api_pubkey; ?>" size="52" />
              </td>
            </tr>
            <tr>
             <td>
          	    <label for="entry_key"><?php echo $entry_key; ?></label>
              </td>
              <td>
                <input type="text" name="oc_api_key" value="<?php echo $oc_api_key; ?>" size="52" />
              </td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </div>
  <div style="text-align: center;">
	Version: <?php echo $version; ?> | <a href="http://loclahostph.com/labs/opencart-api"><?php echo $text_homepage; ?></a>
  </div>
</div>

<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script>

<?php echo $footer; ?>
