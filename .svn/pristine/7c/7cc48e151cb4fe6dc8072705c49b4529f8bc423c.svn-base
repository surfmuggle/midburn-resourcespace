<?php
// called by node_field_options_override function to migrate comma separated options to nodes
include_once 'migration_functions.php';


/**
* Set node - Used for both creating and saving a node in the database.
* Use NULL for ref if you just want to insert a new record.
*
* @param  integer  $ref                   ID of the node. To insert new record ID should be NULL
* @param  integer  $resource_type_field   ID of the metadata field
* @param  string   $name                  Node name to be used (international)
* @param  integer  $parent                ID of the parent of this node
* @param  integer  $order_by              Value of the order in the list (e.g. 10)
*
* @return boolean
*/
function set_node($ref, $resource_type_field, $name, $parent, $order_by)
    {

	if (is_null($resource_type_field) || $resource_type_field=='' || is_null($name) || $name=='')
		{
		return false;
		}

    if(is_null($ref) && ($order_by==""))
        {
        $order_by  = get_node_order_by($resource_type_field,!($parent==""),$parent);
        }

    $query  = 'INSERT INTO node (`resource_type_field`, `name`, `parent`, `order_by`)';
    $query .= ' SELECT \'' . escape_check($resource_type_field) . '\', \'' . escape_check($name) . '\'';

    if(trim($parent)=='')
        {
        $query .= ', NULL';
        }
    else
        {
        $query .= ', \'' . escape_check($parent) . '\'';
        }

    $query .= ', \'' . escape_check($order_by) . '\' FROM DUAL';
    $query .= ' WHERE NOT EXISTS (SELECT * FROM `node` WHERE `resource_type_field` = \'' . escape_check($resource_type_field) . '\' AND `name` = \'' . escape_check($name) . '\')';

    // Check if we only need to save the record
    $current_node = array();
    if(get_node($ref, $current_node))
        {
        // If nothing has changed, just return true, otherwise continue and update record
        if($resource_type_field === $current_node['resource_type_field'] &&
            $name === $current_node['name'] &&
            $parent === $current_node['parent'] &&
            $order_by === $current_node['order_by']
            )
            {
            return $ref;
            }

        // When changing parent we need to make sure order by is changed as well
        // to reflect the fact that the node has just been added (ie. at the end of the list)
        if($parent !== $current_node['parent'])
            {
            $order_by = get_node_order_by($resource_type_field, true, $parent);
            }

        // Order by can be changed asynchronously, so when we save a node we can pass null or an empty
        // order_by value and this will mean we can use the current order
        if(!is_null($ref) && ($order_by==""))
            {
            $order_by = $current_node['order_by'];
            }

        $query = sprintf("
                UPDATE node
                   SET resource_type_field = '%s',
                       `name` = '%s',
                       parent = %s,
                       order_by = '%s'
                 WHERE ref = '%s'
            ",
            escape_check($resource_type_field),
            escape_check($name),
            (trim($parent)=="" ? 'NULL' : '\'' . escape_check($parent) . '\''),
            escape_check($order_by),
            escape_check($ref)
        );

        }

    sql_query($query);
	$new_ref = sql_insert_id();
	if ($new_ref == 0 || $new_ref === false)
		{
		if ($ref == null)
			{
			return sql_value("SELECT `ref` AS 'value' FROM `node` WHERE `resource_type_field`='" . escape_check($resource_type_field) . "' AND `name`='" . escape_check($name) . "'",0);
			}
		else
			{
			return $ref;
			}
		}
	else
		{
		return $new_ref;
		}

    }


/**
* Delete node
*
* @param  integer  $ref  ID of the node
*
* @return void
*/
function delete_node($ref)
    {
    // TODO: if node is parent then don't delete it for now
    if(is_parent_node($ref))
        {
        return;
        }

    $query = "DELETE FROM node WHERE ref = '" . escape_check($ref) . "';";
    sql_query($query);

    return;
    }


/**
* Delete all nodes for a resource type field
*
* @param  integer  $resource_type_field  ID of the resource type field
*
* @return void
*/
function delete_nodes_for_resource_type_field($ref)
    {
    if(is_null($ref) || '' === trim($ref) || 0 === $ref)
        {
        trigger_error('$ref must be an integer greater than 0');
        }

    sql_query("DELETE FROM node WHERE resource_type_field = '" . escape_check($ref) . "';");

    return;
    }


/**
* Get a specific node by ref
* 
* @param  integer  $ref              ID of the node
* @param  array    $returned_node    If a value does exist it will be returned through
*                                    this parameter which is passed by reference
* @return boolean
*/
function get_node($ref, array &$returned_node)
    {
    if(is_null($ref) || (trim($ref)=="") || 0 >= $ref)
        {
        return false;
        }

    $query = "SELECT * FROM node WHERE ref = '" . escape_check($ref) . "';";
    $node  = sql_query($query);

    if($node=="")
        {
        return false;
        }

    $returned_node = $node[0];

    return true;
    }


/**
* Get all nodes from database for a specific metadata field or parent.
* Use $parent = NULL and recursive = TRUE to get all nodes for a field
* 
* @param  integer  $resource_type_field    ID of the metadata field
* @param  integer  $parent                 ID of parent node
* @param  integer  $recursive              Set to true to get children nodes as well
* @return array
*/
function get_nodes($resource_type_field, $parent = NULL, $recursive = FALSE)
    {
    $return_nodes = array();

    $query = sprintf('SELECT * FROM node WHERE resource_type_field = \'%s\' AND %s ORDER BY order_by ASC;',
        escape_check($resource_type_field),
        (trim($parent)=="") ? 'parent IS NULL' : "parent = '" . escape_check($parent) . "'"
    );
    $nodes = sql_query($query);

    foreach($nodes as $node)
        {
        array_push($return_nodes, $node);

        if($recursive)
            {
            foreach(get_nodes($resource_type_field, $node['ref'], TRUE) as $sub_node)
                {
                array_push($return_nodes, $sub_node);
                }
            }
        }

    return $return_nodes;
    }


/**
* Checks whether a node is parent to other nodes or not
*
* @param  integer    $ref    Node ref
*
* @return boolean
*/
function is_parent_node($ref)
    {
    if(is_null($ref))
        {
        return false;
        }

    $query = "SELECT count(ref) AS value FROM node WHERE parent = '" . escape_check($ref) . "';";
    $parent_counter = sql_value($query, 0);

    if($parent_counter > 0)
        {
        return true;
        }

    return false;
    }


/**
* Determine how many level deep a node is. Useful for knowing how much
* indent a node
*
* @param  integer    $ref    Node ref
*
* @return integer            The depth value of a tree node
*/
function get_tree_node_level($ref)
    {
    if(!isset($ref))
        {
        trigger_error('Node ID should be set and not NULL');
        }

    $parent      = escape_check($ref);
    $depth_level = -1;

    do
        {
        $query  = "SELECT parent AS value FROM node WHERE ref = '" . $parent . "';";
        $parent = sql_value($query, 0);

        $depth_level++;
        }
    while('' != trim($parent));

    return $depth_level;
    }


/**
* Function used to reorder nodes based on an array with nodes in the new order
*
* @param  array  $nodes_new_order  Array of nodes 
*
* @return void
*/
function reorder_node(array $nodes_new_order)
    {
    if(0 === count($nodes_new_order))
        {
        trigger_error('$nodes_new_order cannot be an empty array!');
        }

    $order_by = 10;

    $query = 'UPDATE node SET order_by = (CASE ref ';
    foreach($nodes_new_order as $node_ref)
        {
        $query    .= 'WHEN \'' . $node_ref . '\' THEN \'' . $order_by . '\' ';
        $order_by += 10;
        }
    $query .= 'ELSE order_by END);';

    sql_query($query);

    return;
    }


/**
* Renders HTML for adding a new node record in the database
*
* @param  string   $form_action          Set the action path of the form
* @param  boolean  $is_tree              Set to TRUE if the field is category tree type
* @param  integer  $parent               ID of the parent of this node
* @param  integer  $node_depth_level     When rendering for trees, we need to know how many levels deep we need to render it
* @param  array    $parent_node_options  Array of node options to be used as parent for new records
*
* @return void
*/
function render_new_node_record($form_action, $is_tree, $parent = 0, $node_depth_level = 0, array $parent_node_options = array())
    {
    global $baseurl_short, $lang;
    if(!isset($is_tree))
        {
        trigger_error('$is_tree param for render_new_node_record() must be set to either TRUE or FALSE!');
        }

    if (trim($form_action)=="")
        {
        trigger_error('$form_action param for render_new_node_record() must be set and not be an empty string!');
        }

    // Render normal fields first then go to tree type
    if(!$is_tree)
        {
        ?>
        <tr id="new_node_<?php echo $parent; ?>_children">
            <td>
                <input type="text" name="new_option_name" form="new_option" value="">
            </td>
            <td>
                <div class="ListTools">
                    <form id="new_option" method="post" action="<?php echo $form_action; ?>">
                        <button type="submit" onClick="AddNode(<?php echo $parent; ?>); return false;"><?php echo $lang['add']; ?></button>
                    </form>
                </div>
            </td>
        </tr>
        <?php

        return;
        }

    // Trees only
    ?>
    <table id="new_node_<?php echo $parent; ?>_children" cellspacing="0" cellpadding="5">
        <tbody>
            <tr>
            <?php
            // Indent node to the correct depth level
            $i = $node_depth_level;
            while(0 < $i)
                {
                $i--;
                ?>
                <td class="backline" width="10">
                    <img width="11" height="11" hspace="4" src="<?php echo $baseurl_short; ?>gfx/interface/sp.gif">
                </td>
                <?php
                }
                ?>
                <td class="backline" width="10">
                    <img width="11" height="11" hspace="4" src="<?php echo $baseurl_short; ?>gfx/interface/sp.gif">
                </td>
                <td>
                    <input type="text" name="new_option_name" form="new_node_<?php echo $parent; ?>_option" value="">
                </td>
                <td>
                    <select class="node_parent_chosen_selector" name="new_option_parent" form="new_node_<?php echo $parent; ?>_option">
                        <option value="">Select parent</option>
                    <?php
                    foreach($parent_node_options as $node)
                        {
                        $selected = '';
                        if(!(trim($parent)=="") && $node['ref'] == $parent)
                            {
                            $selected = ' selected';
                            }
                        ?>
                        <option value="<?php echo $node['ref']; ?>"<?php echo $selected; ?>><?php echo $node['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <div class="ListTools">
                        <form id="new_node_<?php echo $parent; ?>_option" method="post" action="<?php echo $form_action; ?>">
                            <button type="submit" onClick="AddNode(<?php echo $parent; ?>); return false;"><?php echo $lang['add']; ?></button>
                        </form>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <?php
    return;
    }


/**
* Calculate the next order by for a new record
*
* @param  integer  $resource_type_field   ID of the metadata field
* @param  boolean  $is_tree               Param to flag whether this is for a tree node
* @param  integer  $parent                ID of the parent of this node
*
* @return integer  $order_by  
*/
function get_node_order_by($resource_type_field, $is_tree = FALSE, $parent = NULL)
    {
    $order_by = 10;

    $query         = "SELECT COUNT(*) AS value FROM node WHERE resource_type_field = '" . escape_check($resource_type_field) . "' ORDER BY order_by ASC;";
    $nodes_counter = sql_value($query, 0);

    if($is_tree)
        {
        $query = sprintf('SELECT COUNT(*) AS value FROM node WHERE resource_type_field = \'%s\' AND %s ORDER BY order_by ASC;',
            escape_check($resource_type_field),
            (trim($parent)=="") ? 'parent IS NULL' : 'parent = \'' . escape_check($parent) . '\''
        );

        $nodes_counter = sql_value($query, 0);
        }

    if(0 < $nodes_counter)
        {
        $order_by = ($nodes_counter + 1) * 10;
        }

    return $order_by;
    }


/**
* Renders HTML for a tree node
*
* @param  integer  $ref                   ID of the node
* @param  integer  $resource_type_field   ID of the metadata field
* @param  string   $name                  Node name to be used (international)
* @param  integer  $parent                ID of the parent of this node
* @param  integer  $order_by              Value of the order in the list (e.g. 10)
* @param  boolean  $last_node             Set to true to allow to insert new records after
*                                         last node in each level
*
* @return boolean
*/
function draw_tree_node_table($ref, $resource_type_field, $name, $parent, $order_by, $last_node = false)
    {
    global $baseurl_short, $lang;

    if(is_null($ref) || (trim($ref)==""))
        {
        return false;
        }

    $toggle_node_mode = '';
    $spacer_filename  = 'sp.gif';
    $onClick          = '';

    if(is_parent_node($ref))
        {
        $toggle_node_mode = 'unex';
        $spacer_filename  = 'node_unex.gif';
        $onClick          = sprintf('ToggleTreeNode(%s, %s);', $ref, $resource_type_field);
        }

    // Determine Node depth
    $node_depth_level = get_tree_node_level($ref);

    $all_nodes = get_nodes($resource_type_field, NULL, TRUE);

    // We remove the current node from the list of parents for it( a node should not add to itself)
    $nodes = $all_nodes;
    $nodes_index_to_remove = array_search($ref, array_column($nodes, 'ref'));
    unset($nodes[$nodes_index_to_remove]);
    $nodes = array_values($nodes);
    ?>
    <table id="node_<?php echo $ref; ?>" cellspacing="0" cellpadding="5" data-toggle-node-mode = "<?php echo $toggle_node_mode; ?>">
        <tbody>
            <tr>
            <?php
            // Indent node to the correct depth level
            $i = $node_depth_level;
            while(0 < $i)
                {
                $i--;
                ?>
                <td class="backline" width="10">
                    <img width="11" height="11" hspace="4" src="<?php echo $baseurl_short; ?>gfx/interface/sp.gif">
                </td>
                <?php
                }
                ?>
                <td class="backline" width="10">
                    <img id="node_<?php echo $ref; ?>_toggle_button" width="11" height="11" hspace="4" src="<?php echo $baseurl_short; ?>gfx/interface/<?php echo $spacer_filename; ?>" onclick="<?php echo $onClick; ?>">
                </td>
                <td>
                    <input type="text" name="option_name" form="option_<?php echo $ref; ?>" value="<?php echo $name; ?>">
                </td>
                <td>
                    <select id="node_option_<?php echo $ref; ?>_parent_select" class="node_parent_chosen_selector" name="option_parent" form="option_<?php echo $ref; ?>">
                        <option value="">Select parent</option>
                    <?php
                    foreach($nodes as $node)
                        {
                        $selected = '';
                        if(!(trim($parent)=="") && $node['ref'] == $parent)
                            {
                            $selected = ' selected';
                            }
                        ?>
                        <option value="<?php echo $node['ref']; ?>"<?php echo $selected; ?>><?php echo $node['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <div class="ListTools">
                        <form id="option_<?php echo $ref; ?>" method="post" action="/pages/admin/admin_manage_field_options.php?field=<?php echo $resource_type_field; ?>">
                            <input type="hidden" name="option_order_by" value="<?php echo $order_by; ?>">

                            <button type="submit" onclick="SaveNode(<?php echo $ref; ?>); return false;"><?php echo $lang['save']; ?></button>
                            <button type="submit" onclick="ReorderNode(<?php echo $ref; ?>, 'moveup'); return false;"><?php echo $lang['action-move-up']; ?></button>
                            <button type="submit" onclick="ReorderNode(<?php echo $ref; ?>, 'movedown'); return false;"><?php echo $lang['action-move-down']; ?></button>
							<?php 
							if(!is_parent_node ($ref))
								{?>
								<button type="submit" onclick="DeleteNode(<?php echo $ref; ?>); return false;"><?php echo $lang['action-delete']; ?></button>
								<?php
								}
							?>
                        </form>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <div id="node_<?php echo $ref; ?>_children"></div>

    <?php
    // Add a way of inserting new records after the last node of each level
    if($last_node)
        {
        if(trim($parent)=="")
            {
            $parent = 0;
            }
        render_new_node_record('/pages/admin/admin_manage_field_options.php?field=' . $resource_type_field, TRUE, $parent, $node_depth_level, $all_nodes);
        }

    return true;
    }


/**
 * Overrides either a field[options] array structure or a flat option array with values derived from nodes.
 * If a field, then will also add field=>nodes[] sub array ready for field rendering.
 *
 * @param  mixed    $field                 Either field array structure or flat options list array
 * @param  integer  $resource_type_field   ID of the metadata field, if specified will treat as flat options list
 *
 * @return boolean
 */
function node_field_options_override(&$field,$resource_type_field=null)
    {
    if (!is_null($resource_type_field))     // we are dealing with a single specified resource type so simply return array of options
        {
        $options = get_nodes($resource_type_field);
        if(count($options) > 0)     // only override if field options found within nodes
            {
            $field = array();
            foreach ($options as $option)
                {
                array_push($field,$option['name']);
                }
            }
        return true;
        }

    if (
        !isset($field['ref']) ||
        !isset($field['type']) ||
        !in_array($field['type'], array(2, 3, 7, 9, 12))
    )
        {
        return false;       // get out of here if not a node supported field type
        }

    migrate_resource_type_field_check($field);

    $field['nodes'] = array();          // setup new nodes associate array to be used by node-aware field renderers
    $field['node_options'] = array();   // setup new node options list for render of flat fields such as drop down lists (saves another iteration through nodes to grab names)

    if ($field['type'] == 7)        // category tree
        {
        $category_tree_nodes = get_nodes($field['ref'], null, true);
        if (count($category_tree_nodes) > 0)
            {
            $field['options'] = '';
            foreach ($category_tree_nodes as $node)
                {
                // for legacy category tree field rendering:
                // $field['options'] .= $node['ref'] . ',' . (is_null($node['parent']) ? 0 : $node['parent']) . ',' . $node['name'] . PHP_EOL;

                $field['nodes'][$node['ref']] = $node;
                }
            }
        }
    else        // normal comma separated options used for checkboxes, selects, etc.
        {
        $nodes = get_nodes($field['ref']);
        if (count($nodes) > 0)
            {
            $field['options'] = '';
            foreach ($nodes as $node)
                {
                // for legacy field rendering:
                //$field['options'] .= ($field['options'] == '' ? '' : ',') . $node['name'];

                // for new field rendering:
                $field['nodes'][$node['ref']]=$node;
                array_push($field['node_options'],$node['name']);
                }
            }
        }
    return true;
    }
