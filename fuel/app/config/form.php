<?php
/**
 * Part of the Fuel framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2014 Fuel Development Team
 * @link       http://fuelphp.com
 */

/**
 * NOTICE:
 *
 * If you need to make modifications to the default configuration, copy
 * this file to your app/config folder, and make them in there.
 *
 * This will allow you to upgrade fuel without losing your custom config.
 */


return array(
	// regular form definitions
	'prep_value'                 => true,
	'auto_id'                    => true,
	'auto_id_prefix'             => 'form_',
	'form_method'                => 'post',
  // 'form_template'              => '{open}{fields}{close}',
	'form_template'              => "\n\t\t{open}\n\t\t<table class=\"table\">\n{fields}\n\t\t</table>\n\t\t{close}\n",
	'fieldset_template'          => "\n\t\t<tr><td colspan=\"2\">{open}<table class=\"table\">\n{fields}</table></td></tr>\n\t\t{close}\n",
  'field_template'             => "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{field} <span>{description}</span> {error_msg}</td>\n\t\t</tr>\n",
	//'field_template'             => "<div class=\"form-group {error_class}\">\n{label}{required}{field} <span>{description}</span> {error_msg}</div>",
	'multi_field_template'       => "\t\t<tr>\n\t\t\t<td class=\"{error_class}\">{group_label}{required}</td>\n\t\t\t<td class=\"{error_class}\">{fields}\n\t\t\t\t{field} {label}<br />\n{fields}<span>{description}</span>\t\t\t{error_msg}\n\t\t\t</td>\n\t\t</tr>\n",
	'error_template'             => '<span>{error_msg}</span>',
	'group_label'	             => '<span>{label}</span>',
	'required_mark'              => '',
	'inline_errors'              => false,
	'error_class'                => null,
	'label_class'                => null,

	// tabular form definitions
	'tabular_form_template'      => "<table>{fields}</table>\n",
	'tabular_field_template'     => "{field}",
	'tabular_row_template'       => "<tr>{fields}</tr>\n",
	'tabular_row_field_template' => "\t\t\t<td>{label}{required}&nbsp;{field} {error_msg}</td>\n",
	'tabular_delete_label'       => "Delete?",
);
