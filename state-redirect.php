<?php
/*
Plugin Name: State Redirect
Description: A plugin to redirect users to state-specific pages based on input. Use this [state_redirect] shortcode in the for redirect.
Version: 1.0.0
Author: M. Sufyan Shaikh
Author URI: https://souloftware.com/
*/

// Register the settings page
function state_redirect_register_settings()
{
    add_options_page(
        'State Redirect Settings',
        'State Redirect',
        'manage_options',
        'state-redirect',
        'state_redirect_settings_page'
    );
}
add_action('admin_menu', 'state_redirect_register_settings');

// Create the settings page
function state_redirect_settings_page()
{
?>
    <div class="wrap">
        <h1>State Redirect Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('state_redirect_settings_group');
            do_settings_sections('state-redirect');
            submit_button();
            ?>
        </form>
    </div>
<?php
}

// Register settings, sections, and fields
function state_redirect_register_settings_fields()
{
    register_setting('state_redirect_settings_group', 'state_redirect_form_id');
    register_setting('state_redirect_settings_group', 'state_redirect_state_field_id');
    register_setting('state_redirect_settings_group', 'state_redirect_state_map');

    add_settings_section(
        'state_redirect_main_section',
        'Main Settings',
        null,
        'state-redirect'
    );

    add_settings_field(
        'state_redirect_form_id',
        'Form ID',
        'state_redirect_form_id_callback',
        'state-redirect',
        'state_redirect_main_section'
    );

    add_settings_field(
        'state_redirect_state_field_id',
        'State Field ID',
        'state_redirect_state_field_id_callback',
        'state-redirect',
        'state_redirect_main_section'
    );

    add_settings_field(
        'state_redirect_state_map',
        'State Map Object',
        'state_redirect_state_map_callback',
        'state-redirect',
        'state_redirect_main_section'
    );
}
add_action('admin_init', 'state_redirect_register_settings_fields');

function state_redirect_form_id_callback()
{
    $form_id = esc_attr(get_option('state_redirect_form_id'));
    echo "<input type='text' name='state_redirect_form_id' value='$form_id' />";
}

function state_redirect_state_field_id_callback()
{
    $state_field_id = esc_attr(get_option('state_redirect_state_field_id'));
    echo "<input type='text' name='state_redirect_state_field_id' value='$state_field_id' />";
}

function state_redirect_state_map_callback()
{
    $state_map = esc_textarea(get_option('state_redirect_state_map'));
    echo "<textarea name='state_redirect_state_map' rows='10' cols='50'>$state_map</textarea>";
}

function state_redirect_shortcode()
{
    $form_id = esc_attr(get_option('state_redirect_form_id'));
    $state_field_id = esc_attr(get_option('state_redirect_state_field_id'));
    $state_map = get_option('state_redirect_state_map');

    ob_start();
?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
    <script>
        $(document).ready(function() {
            $('#<?php echo $form_id; ?> form').on('submit', function() {
                const userInput = $('#<?php echo $state_field_id; ?>').val().toLowerCase();
                const stateMap = <?php echo $state_map ? $state_map : '{}'; ?>;
                console.log(userInput);

                if (stateMap.hasOwnProperty(userInput)) {
                    const statePage = stateMap[userInput];
                    setTimeout(() => {
                        window.location.href = statePage;
                    }, 2000);
                } else {
                    alert('Invalid state entered. Please try entering your state abbreviation again.');
                }
                return false;
            });
        });
    </script>

<?php
    return ob_get_clean();
}

add_shortcode('state_redirect', 'state_redirect_shortcode');
