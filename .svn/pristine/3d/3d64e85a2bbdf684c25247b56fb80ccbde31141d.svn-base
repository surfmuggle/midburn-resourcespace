<?php

include "../../include/db.php";
include "../../include/general.php";
include "../../include/authenticate.php";
include_once '../../include/config_functions.php';

// Do not allow access to anonymous users
if(isset($anonymous_login) && ($anonymous_login == $username))
    {
    header('HTTP/1.1 401 Unauthorized');
    die('Permission denied!');
    }

$userpreferences_plugins= array();
$plugin_names=array();
$plugins_dir = dirname(__FILE__)."/../../plugins/";
foreach($active_plugins as $plugin)
    {
    $plugin = $plugin["name"];
    array_push($plugin_names,trim(mb_strtolower($plugin)));
    $plugin_yaml = get_plugin_yaml($plugins_dir.$plugin.'/'.$plugin.'.yaml', false);
    if(isset($plugin_yaml["userpreferencegroup"]))
        {
        $upg = trim(mb_strtolower($plugin_yaml["userpreferencegroup"]));
        $userpreferences_plugins[$upg][$plugin]=$plugin_yaml;
        }
    }

if(getvalescaped("quicksave",FALSE))
    {
    $ctheme = getvalescaped("colour_theme","");
    if($ctheme==""){exit("missing");}
    $ctheme = preg_replace("/^col-/","",trim(mb_strtolower($ctheme)));
    if($ctheme =="default")
        {
        if(empty($userpreferences))
            {
            // create a record
            sql_query("INSERT INTO user_preferences (user, parameter, `value`) VALUES (" . $userref . ", 'colour_theme', NULL);");
            rs_setcookie("colour_theme", "",100, "/", "", substr($baseurl,0,5)=="https", true);
            exit("1");
            }
        else
            {
            sql_query("UPDATE user_preferences SET `value` = NULL WHERE user = " . $userref . " AND parameter = 'colour_theme';");
            rs_setcookie("colour_theme", "",100, "/", "", substr($baseurl,0,5)=="https", true);
            exit("1");
            }
        }
    if(in_array("col-".$ctheme,$plugin_names))
        {
        // check that record exists for user
        if(empty($userpreferences))
            {
            // create a record
            sql_query("INSERT into user_preferences (user, parameter, `value`) VALUES (" . $userref . ", 'colour_theme', '" . escape_check(preg_replace('/^col-/', '', $ctheme)) . "');");
            rs_setcookie("colour_theme", escape_check(preg_replace("/^col-/","",$ctheme)),100, "/", "", substr($baseurl,0,5)=="https", true);
            exit("1");
            }
        else
            {
            sql_query("UPDATE user_preferences SET `value` = '" . escape_check(preg_replace('/^col-/', '', $ctheme)) . "' WHERE user = " . $userref . " AND parameter = 'colour_theme';");
            rs_setcookie("colour_theme", escape_check(preg_replace("/^col-/","",$ctheme)),100, "/", "", substr($baseurl,0,5)=="https", true);
            exit("1");
            }
        }

    exit("0");
    }

$enable_disable_options = array($lang['userpreference_disable_option'], $lang['userpreference_enable_option']);

include "../../include/header.php";
?>
<div class="BasicsBox"> 
    <h1><?php echo $lang["userpreferences"]?></h1>
    <p><?php echo $lang["modifyuserpreferencesintro"]?></p>
    
    <?php
    /* Display */
    $options_available = 0; # Increment this to prevent a "No options available" message

    /* User Colour Theme Selection */
    if((isset($userfixedtheme) && $userfixedtheme=="") && isset($userpreferences_plugins["colourtheme"]) && count($userpreferences_plugins["colourtheme"])>0)
        {
        ?>
        <h2><?php echo $lang['userpreference_colourtheme']; ?></h2>
        <div class="Question">
            <label for="">
                <?php echo $lang["userpreferencecolourtheme"]; ?>
            </label>
            <script>
                function updateColourTheme(theme) {
                    jQuery.post(
                        window.location,
                        {"colour_theme":theme,"quicksave":"true"},
                        function(data){
                            location.reload();
                        });
                }
            </script>
            <?php
            # If there are multiple options provide a radio button selector
            if(count($userpreferences_plugins["colourtheme"])>1)
                { ?>
                <table id="" class="radioOptionTable">
                    <tbody>
                        <tr>
                        <!-- Default option -->
                        <td valign="middle">
                            <input 
                                type="radio" 
                                name="defaulttheme" 
                                value="default" 
                                onChange="updateColourTheme('default');"
                                <?php
                                    if
                                    (
                                        (isset($userpreferences["colour_theme"]) && $userpreferences["colour_theme"]=="") 
                                        || 
                                        (!isset($userpreferences["colour_theme"]) && $defaulttheme=="")
                                    ) { echo "checked";}
                                ?>
                            />
                        </td>
                        <td align="left" valign="middle">
                            <label class="customFieldLabel" for="defaulttheme">
                                <?php echo $lang["default"];?>
                            </label>
                        </td>
                        <?php
                        foreach($userpreferences_plugins["colourtheme"] as $colourtheme)
                            { ?>
                            <td valign="middle">
                                <input 
                                    type="radio" 
                                    name="defaulttheme" 
                                    value="<?php echo preg_replace("/^col-/","",$colourtheme["name"]);?>" 
                                    onChange="updateColourTheme('<?php echo preg_replace("/^col-/","",$colourtheme["name"]);?>');"
                                    <?php
                                        if
                                        (
                                            (isset($userpreferences["colour_theme"]) && "col-".$userpreferences["colour_theme"]==$colourtheme["name"]) 
                                            || 
                                            (!isset($userpreferences["colour_theme"]) && $defaulttheme==$colourtheme["name"])
                                        ) { echo "checked";}
                                    ?>
                                />
                            </td>
                            <td align="left" valign="middle">
                                <label class="customFieldLabel" for="defaulttheme">
                                    <?php echo $colourtheme["name"];?>
                                </label>
                            </td>
                            <?php
                            }
                        ?>
                        </tr>
                    </tbody>
                </table>
                <?php
                }
            ?>
            <div class="clearerleft"> </div>
        </div>
        <?php
        $options_available++;
        }
    /* End User Colour Theme Selection */


    ?>

<div class="CollapsibleSections">
    <?php
    // Result display section
    $all_field_info = get_fields_for_search_display(array_unique(array_merge(
        $sort_fields,
        $thumbs_display_fields,
        $list_display_fields,
        $xl_thumbs_display_fields,
        $small_thumbs_display_fields))
    );

    // Create a sort_fields array with information for sort fields
    $n  = 0;
    $sf = array();
    foreach($sort_fields as $sort_field)
        {
        // Find field in selected list
        for($m = 0; $m < count($all_field_info); $m++)
            {
            if($all_field_info[$m]['ref'] == $sort_field)
                {
                $field_info      = $all_field_info[$m];
                $sf[$n]['ref']   = $sort_field;
                $sf[$n]['title'] = $field_info['title'];

                $n++;
                }
            }
        }

    $sort_order_fields = array('relevance' => $lang['relevance']);
    if($random_sort)
        {
        $sort_order_fields['random'] = $lang['random'];
        }

    if($popularity_sort)
        {
        $sort_order_fields['popularity'] = $lang['popularity'];
        }

    if($orderbyrating)
        {
        $sort_order_fields['rating'] = $lang['rating'];
        }

    if($date_column)
        {
        $sort_order_fields['date'] = $lang['date'];
        }

    if($colour_sort)
        {
        $sort_order_fields['colour'] = $lang['colour'];
        }

    if($order_by_resource_id)
        {
        $sort_order_fields['resourceid'] = $lang['resourceid'];
        }

    if($order_by_resource_type)
        {
        $sort_order_fields['resourcetype'] = $lang['type'];
        }

    // Add thumbs_display_fields to sort order links for thumbs views
    for($x = 0; $x < count($sf); $x++)
        {
        if(!isset($metadata_template_title_field))
            {
            $metadata_template_title_field = false;
            }

        if($sf[$x]['ref'] != $metadata_template_title_field)
            {
            $sort_order_fields['field' . $sf[$x]['ref']] = htmlspecialchars($sf[$x]['title']);
            }
        }

    $page_def[] = config_add_html('<h2 class="CollapsibleSectionHead">' . $lang['resultsdisplay'] . '</h2><div id="UserPreferenceResultsDisplaySection" class="CollapsibleSection">');
    $page_def[] = config_add_single_select(
        'default_sort',
        $lang['userpreference_default_sort_label'],
        $sort_order_fields,
        true,
        300,
        '',
        true
    );
    $page_def[] = config_add_single_select('default_perpage', $lang['userpreference_default_perpage_label'], array(24, 48, 72, 120, 240), false, 300, '', true);
    $page_def[] = config_add_single_select(
        'default_display',
        $lang['userpreference_default_display_label'],
        array(
            'smallthumbs' => $lang['smallthumbstitle'],
            'thumbs'      => $lang['largethumbstitle'],
            'xlthumbs'    => $lang['xlthumbstitle'],
            'list'        => $lang['listtitle']
        ),
        true,
        300,
        '',
        true
    );
    $page_def[] = config_add_boolean_select('use_checkboxes_for_selection', $lang['userpreference_use_checkboxes_for_selection_label'], $enable_disable_options, 300, '', true);
    $page_def[] = config_add_boolean_select('resource_view_modal', $lang['userpreference_resource_view_modal_label'], $enable_disable_options, 300, '', true);
    $page_def[] = config_add_html('</div>');
    ?>

    <?php
    // User interface section
    $page_def[] = config_add_html('<h2 class="CollapsibleSectionHead">' . $lang['userpreference_user_interface'] . '</h2><div id="UserPreferenceUserInterfaceSection" class="CollapsibleSection">');
    $page_def[] = config_add_single_select('thumbs_default', $lang['userpreference_thumbs_default_label'], array('show' => $lang['showthumbnails'], 'hide' => $lang['hidethumbnails']), true, 300, '', true);
    $page_def[] = config_add_boolean_select('basic_simple_search', $lang['userpreference_basic_simple_search_label'], $enable_disable_options, 300, '', true);
    $page_def[] = config_add_html('</div>');

    // Email section
    $page_def[] = config_add_html('<h2 class="CollapsibleSectionHead">' . $lang['email'] . '</h2><div id="UserPreferenceEmailSection" class="CollapsibleSection">');
    $page_def[] = config_add_boolean_select('cc_me', $lang['userpreference_cc_me_label'], $enable_disable_options, 300, '', true);
    $page_def[] = config_add_html('</div>');

    // Let plugins hook onto page definition and add their own configs if needed
    // or manipulate the list
    $plugin_specific_definition = hook('add_user_preference_page_def', '', array($page_def));
    if(is_array($plugin_specific_definition) && !empty($plugin_specific_definition))
        {
        $page_def = $plugin_specific_definition;
        }

    config_generate_html($page_def);
    ?>
</div>
    <script>registerCollapsibleSections();</script>
    <?php config_generate_AutoSaveConfigOption_function($baseurl . '/pages/ajax/user_preferences.php'); ?>
</div>

<?php
include '../../include/footer.php';