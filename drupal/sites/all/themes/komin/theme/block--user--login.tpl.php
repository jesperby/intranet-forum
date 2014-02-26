<section class="box">
	<h1 class="box-title">Logga in</h1>
	<div class="box-content body-copy">
		<form id="user-login-form" method="post" action="/<?php echo current_path(); ?>?destination=<?php echo htmlspecialchars(current_path()) ?>">
		<div>
			<div class="form-type-textfield form-item-name control-group form-item form-group">
				<label class="control-label" for="edit-name">Användarnamn: <span class="form-required" title="Detta fält är obligatoriskt.">*</span></label>
				<div class="controls"><input class="input-wide form-text required" type="text" id="edit-name" name="name" value="" size="15" maxlength="60" mouseev="true" autocomplete="off" keyev="true" clickev="true"></div>
			</div>
			<div class="form-type-password form-item-pass control-group form-item form-group">
				<label class="control-label" for="edit-pass">Lösenord: <span class="form-required" title="Detta fält är obligatoriskt.">*</span></label>
				<div class="controls"><input class="input-wide form-text required" type="password" id="edit-pass" name="pass" size="15" maxlength="128" mouseev="true" autocomplete="off" keyev="true" clickev="true"></div>
			</div>
			<input type="hidden" value="<?php print $elements['form_build_id']['#value']; ?> " name="form_build_id">
			<input type="hidden" value="user_login_block" name="form_id">
			<div id="edit-actions"><input class="btn btn-primary form-submit" type="submit" id="edit-submit" name="op" value="Logga in"></div>
		</div>
		</form>
	</div>
</section>
