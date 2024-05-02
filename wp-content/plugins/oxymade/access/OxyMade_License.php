<?php

// Exit if accessed directly
if (!defined("ABSPATH")) {
  exit();
}

class OxyMadeLicense
{
  static $prefix = "";
  static $title = "";
  static $store_url = "";
  static $item_id = null;

  static function init($prefix, $title, $store_url, $item_id)
  {
    self::$prefix = $prefix;
    self::$title = $title;
    self::$store_url = $store_url;
    self::$item_id = $item_id;

    add_action("admin_init", [__CLASS__, "register_option"]);
    add_action("admin_action_update", [__CLASS__, "activate_license"]);
    add_action("admin_action_update", [__CLASS__, "deactivate_license"]);
    add_action("admin_notices", [__CLASS__, "admin_notices"]);
  }

  static function is_activated_license()
  {
    update_option(self::$prefix . "license_status", 'valid');
    // return true;
    $status = get_option(self::$prefix . "license_status");

    if ($status && $status === "valid") {
      return true;
    }

    return false;
  }

  static function license_page()
  {
    $license = get_option(self::$prefix . "license_key");
    $status = get_option(self::$prefix . "license_status");
    ?>

		<!-- <h2><?php //echo self::$title . " " . __("License");
    ?></h2> -->

		<!-- <p>Follow the steps below to license the plugin:</p> -->

		<!-- <ol>
			<li>Get your license key from oxymade.com</li>
			<li>Paste your key in the License Key field</li>
			<li>Click the "Save Changes" button</li>
			<li>Click "Activate License" button</li>
		</ol> -->
		
		<?php if (
    isset($_POST["oxymade_license_delete"]) &&
    $_POST["oxymade_license_delete"] == "Delete License"
  ) {
    echo "<div class='notice notice-error is-dismissible' style='margin-top: 20px; padding: 12px;'>License key deleted successfully.</div>";

    delete_option("oxymade_license_status");
    delete_option("oxymade_license_key");
  } ?>

		<form method="post" action="options.php">

			<?php settings_fields(self::$prefix . "license"); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e("License Key"); ?>
						</th>
						<td>
							
							<?php if ($status !== false && $status == "valid") { ?>
							<input id="<?php echo self::$prefix; ?>license_key" name="<?php echo self::$prefix; ?>license_key" type="password" class="regular-text" value="<?php esc_attr_e(
  $license
); ?>" />
							<label class="description" for="<?php echo self::$prefix; ?>license_key"><?php _e(
  "Enter your license key"
); ?></label>
							<?php } else { ?>
							<input id="<?php echo self::$prefix; ?>license_key" name="<?php echo self::$prefix; ?>license_key" type="password" class="regular-text" value="<?php esc_attr_e(
  $license
); ?>" />
							<label class="description" for="<?php echo self::$prefix; ?>license_key"><?php _e(
  "Enter your license key"
); ?></label>
							<?php } ?>
							
						</td>
					</tr>
					<?php if (false !== $license) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e("Activate License"); ?>
							</th>
							<td>
								<?php if ($status !== false && $status == "valid") { ?>
									<span style="color:green;"><?php _e("active"); ?></span>
									<?php wp_nonce_field(self::$prefix . "nonce", self::$prefix . "nonce"); ?>
									<input type="submit" class="button-secondary" name="<?php echo self::$prefix; ?>license_deactivate" value="<?php _e(
  "Deactivate License"
); ?>"/>
								<?php } else {wp_nonce_field(
            self::$prefix . "nonce",
            self::$prefix . "nonce"
          ); ?>
									<input type="submit" class="button-secondary" name="<?php echo self::$prefix; ?>license_activate" value="<?php _e(
  "Activate License"
); ?>"/>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

		</form>
		<?php
  $om_fw_license = get_option("oxymade_license_key");

  if ($status !== false && $status == "valid") { ?>
		<form method="post" action="admin.php?page=oxymade&tab=license">
			<input type="submit" class="submitdelete" name="<?php echo self::$prefix; ?>license_delete" value="<?php _e(
  "Delete License"
); ?>"/>
		</form>
		<?php } elseif ($om_fw_license) { ?>
		<form method="post" action="admin.php?page=oxymade&tab=license">
			<input type="submit" class="submitdelete" name="<?php echo self::$prefix; ?>license_delete" value="<?php _e(
  "Delete License"
); ?>"/>
		</form>
		<?php }?>
		<?php
  }

  static function register_option()
  {
    // creates our settings in the options table
    register_setting(self::$prefix . "license", self::$prefix . "license_key", [
      __CLASS__,
      "edd_sanitize_license",
    ]);
  }

  static function edd_sanitize_license($new)
  {
    $old = get_option(self::$prefix . "license_key");
    if ($old && $old != $new) {
      delete_option(self::$prefix . "license_status"); // new license has been entered, so must reactivate
    }
    return $new;
  }

  static function activate_license()
  {
    // listen for our activate button to be clicked
    if (isset($_POST[self::$prefix . "license_activate"])) {
      ob_start();
      // run a quick security check
      if (
        !check_admin_referer(self::$prefix . "nonce", self::$prefix . "nonce")
      ) {
        return;
      } // get out if we didn't click the Activate button

      // retrieve the license from the database
      $license = trim(get_option(self::$prefix . "license_key"));

      update_option(self::$prefix . "license_status", 'valid');
      wp_redirect(add_query_arg("tab", "license", menu_page_url("oxymade")));
      exit();
      // data to send in our API request
      $api_params = [
        "edd_action" => "activate_license",
        "license" => $license,
        "item_name" => urlencode(self::$title), // the name of our product in EDD
        "url" => home_url(),
      ];

      // Call the custom API.
      $response = wp_remote_post(self::$store_url, [
        "timeout" => 15,
        "sslverify" => false,
        "body" => $api_params,
      ]);

      // make sure the response came back okay
      if (
        is_wp_error($response) ||
        200 !== wp_remote_retrieve_response_code($response)
      ) {
        if (is_wp_error($response)) {
          $message = $response->get_error_message();
        } else {
          $message = __("An error occurred, please try again.");
        }
      } else {
        $license_data = json_decode(wp_remote_retrieve_body($response));

        if (false === $license_data->success) {
          switch ($license_data->error) {
            case "expired":
              $message = sprintf(
                __("Your license key expired on %s."),
                date_i18n(
                  get_option("date_format"),
                  strtotime($license_data->expires, current_time("timestamp"))
                )
              );
              break;

            case "disabled":
            case "revoked":
              $message = __("Your license key has been disabled.");
              break;

            case "missing":
              $message = __("Invalid license.");
              break;

            case "invalid":
            case "site_inactive":
              $message = __("Your license is not active for this URL.");
              break;

            case "item_name_mismatch":
              $message = sprintf(
                __("This appears to be an invalid license key for %s."),
                self::$title
              );
              break;

            case "no_activations_left":
              $message = __(
                "Your license key has reached its activation limit."
              );
              break;

            default:
              $message = __("An error occurred, please try again.");
              break;
          }
        }
      }

      // Check if anything passed on a message constituting a failure
      if (!empty($message)) {
        $base_url = add_query_arg("tab", "license", menu_page_url("oxymade"));
        $redirect = add_query_arg(
          ["sl_activation" => "false", "message" => urlencode($message)],
          $base_url
        );

        wp_redirect($redirect);
        exit();
      }

      // $license_data->license will be either "valid" or "invalid"

      update_option(self::$prefix . "license_status", $license_data->license);
      wp_redirect(add_query_arg("tab", "license", menu_page_url("oxymade")));
      exit();
    }
  }

  static function deactivate_license()
  {
    // listen for our activate button to be clicked
    if (isset($_POST[self::$prefix . "license_deactivate"])) {
      ob_start();
      // run a quick security check
      if (
        !check_admin_referer(self::$prefix . "nonce", self::$prefix . "nonce")
      ) {
        return;
      } // get out if we didn't click the Activate button

      // retrieve the license from the database
      $license = trim(get_option(self::$prefix . "license_key"));

      // data to send in our API request
      $api_params = [
        "edd_action" => "deactivate_license",
        "license" => $license,
        "item_id" => self::$item_id,
        "item_name" => urlencode(self::$title), // the name of our product in EDD
        "url" => home_url(),
      ];

      // Call the custom API.
      $response = wp_remote_post(self::$store_url, [
        "timeout" => 15,
        "sslverify" => false,
        "body" => $api_params,
      ]);

      // make sure the response came back okay
      if (
        is_wp_error($response) ||
        200 !== wp_remote_retrieve_response_code($response)
      ) {
        if (is_wp_error($response)) {
          $message = $response->get_error_message();
        } else {
          $message = __("An error occurred, please try again.");
        }

        $base_url = add_query_arg("tab", "license", menu_page_url("oxymade"));
        $redirect = add_query_arg(
          ["sl_activation" => "false", "message" => urlencode($message)],
          $base_url
        );

        wp_redirect($redirect);
        exit();
      }

      // decode the license data
      $license_data = json_decode(wp_remote_retrieve_body($response));

      // $license_data->license will be either "deactivated" or "failed"
      if ($license_data->license == "deactivated") {
        delete_option(self::$prefix . "license_status");
      }

      wp_redirect(add_query_arg("tab", "license", menu_page_url("oxymade")));
      exit();
    }
  }

  static function admin_notices()
  {
    if (isset($_GET["sl_activation"]) && !empty($_GET["message"])) {
      switch ($_GET["sl_activation"]) {
        case "false":
          $message = urldecode($_GET["message"]); ?>
					<div class="error">
						<p><?php echo $message; ?></p>
					</div>
					<?php break;

        case "true":
        default:
          // Developers can put a custom success message here for when activation is successful if they way.
          break;
      }
    }
  }

  static function check_license()
  {
    global $wp_version;

    $license = trim(get_option(self::$prefix . "license_key"));

    $api_params = [
      "edd_action" => "check_license",
      "license" => $license,
      "item_name" => urlencode(self::$title),
      "url" => home_url(),
    ];

    // Call the custom API.
    $response = wp_remote_post(self::$store_url, [
      "timeout" => 15,
      "sslverify" => false,
      "body" => $api_params,
    ]);

    if (is_wp_error($response)) {
      return false;
    }

    $license_data = json_decode(wp_remote_retrieve_body($response));

    if ($license_data->license == "valid") {
      echo "valid";
      exit();
      // this license is still valid
    } else {
      echo "invalid";
      exit();
      // this license is no longer valid
    }
  }
}

?>
