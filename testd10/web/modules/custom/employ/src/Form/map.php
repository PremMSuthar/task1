<?php

namespace Drupal\employ\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Render\Element\Tableselect;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\file\Entity\File;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Drupal\Component\Utility\Html;
use Drupal\Core\Site\Settings;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\pdf_generator\DomPdfGenerator;
use Drupal\Core\Entity\EntityTypeManagerInterface;

use Drupal\taxonomy\Entity\Term;

class WqRouteMgmtForm extends FormBase
{

  public function getFormId()
  {
    return 'wq_check_data_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
	  
	  //Note: We need to prevent initializing the map for the page more than once.
	  $map_added = &drupal_static('wq_data_map_added', false);
	  /*if(!$map_added) {*/
		$map_added = true;	
		$parms = $form_state->cleanValues()->getValues();
		$parms['sites'] = true;
		$parms['canvas-id'] ='map-canvas';
		
		$sites = $this->wq_data_map_page();	
		$settings=$this->wq_data_add_gmap($parms, $sites);	
		$form['#attached']['drupalSettings']['settings'] = $settings;	  
		$form['#attached']['library'][] = 'wq_data/custom.map';		
	  /*}*/
	  
	  
	  
	  $this->_wq_data_load_site_routes($form_state);
	  
	  $header = ['', 'Station', 'Location', 'Deactivation'];
	  $rows_route = [];
	  $rows_non_route =[];
	  if(isset($parms['select_route'])) {
		$form_state->get('storage')['wq_data']['route_tid'] = $parms['select_route'];
		$selected_route = $form_state->get('storage')['wq_data']['routes'][$parms['select_route']];
		
		$site_ids = [];
		
		
		$site_list = &drupal_static(__FUNCTION__);
		if (!isset($site_list)) {
		  // generate contents of static variable
		  $query_sites = Database::getConnection()
			->select('tblSites')
			->distinct();
		  $query_sites->fields('tblSites');
		  $query_sites->condition('field_route_tid',$parms['select_route']);
		  $query_sites->orderBy('ID', 'ASC');
		  $result_sites = $query_sites->execute();
		 
		 foreach($result_sites as $site) {
			$site_ids[$site->ID] = $site->ID;
			$site = $form_state->get('storage')['wq_data']['sites'][$site->ID];
			// $rows_route[] = ['data' => ["<a href='#' class='tabledrag-handle' title='Drag to re-order'><div class='handle'>&nbsp;</div></a>", 
			//   "<a href='/site/$site->ID'>$site->Station</a>", $site->Location, $this->wq_data_time_to_day($site->Deactivation)], 'id' => ['site-'.$site->ID], 'markup' => TRUE];	
			$rows_route[] = [
				'data' => [
				  [
					'data' => "<a href='#' class='tabledrag-handle' title='Drag to re-order'><div class='handle'>&nbsp;</div></a>",
				  ],
				  [
					'data' => "<a href='/site/{$site->ID}'>{$site->Station}</a>",
				  ],
				  [
					'data' => [
					  '#markup' => strip_tags($site->Location), // Remove HTML tags from Location
					],
				  ],
				  [
					'data' => [
					  '#markup' => strip_tags($this->wq_data_time_to_day($site->Deactivation)), // Remove HTML tags from Deactivation
					],
				  ],
				],
				'id' => ['site-' . $site->ID],
			  ];
			
		  }
		}
		
		foreach($form_state->get('storage')['wq_data']['sites'] as $site_id =>$site) {
		  if(!in_array($site_id, $site_ids)) {
			$rows_non_route[] = ['data' => ["<a href='#' class='tabledrag-handle' title='Drag to re-order'><div class='handle'>&nbsp;</div></a>", 
			  "<a href='/site/$site->ID'>$site->Station</a>", $site->Location, $this->wq_data_time_to_day($site->Deactivation)], 'id' => ['site-'.$site->ID], 'markup' => TRUE];        
		  }      
		}
		$form['route_sites_fs'] = [
		  '#type' => 'details',
		  '#title' => t('Sites for selected route: %route', ['%route' => $selected_route->getName()]),
		  '#description' => t('Drag sites on this route to reorder them.'),
		  '#open' => true,
		];
		
		  
		$form['route_sites_fs']['route_sites'] = [
			'#theme' => 'table',
			'#header' => $header,
			'#rows' => $rows_route,
			'#empty' => t('No data available'),
		];	  
		  
		  
		$form['route_sites_fs']['actions'] = [
		  '#prefix' => '<div class="form-actions">',
		  '#suffix' => '</div>',
		];
		$form['route_sites_fs']['actions']['submit'] = array(
		  '#type' => 'submit',
		  '#name' => 'save',
		  '#value' => t('Save'),
		  );
		$form['route_sites_fs']['actions']['cancel'] = array(
		  '#type' => 'submit',
		  '#name' => 'cancel',
		  '#value' => t('Cancel'),
		  );

		$form['non_route_sites_fs'] = [
		  '#type' => 'details',
		  '#title' => t('Other sites'),
		  '#open' => true,
		  '#description' => t('Drag sites from or to here to add or remove them from the selected route above.'),
		];

		$form['non_route_sites_fs']['non_route_sites'] = [
			'#theme' => 'table',
			'#header' => $header,
			'#rows' => $rows_non_route,
			'#empty' => t('No data available'),
		];	
		
		
		
		$form['route_sites_data'] = ['#type' => 'hidden'];
		$form['route_sites_original'] = ['#type' => 'hidden'];
		$map_controls = "<div class='form-actions'><input id='map-show-route' type='button' value='Show only $selected_route->getName() route sites'><input id='map-show-all' type='button' value='Show all sites'></div>";
		
		
	  } else {
		$route_options = [0 => t('Select a route')];
		foreach($form_state->get('storage')['wq_data']['routes'] as $route_id =>$route) {
			$route_options[$route->id()] = $route->getName();
		}
		$form['select_route'] = [
		  '#type' => 'select',
		  '#name' => 'select_route',
		  '#title' => t('Route'),
		  '#options' => $route_options,
		  '#description' => t('Select a route to modify.'),
		  '#attributes' => array('onchange' => 'this.form.submit();'),    
		];
		$map_controls = "";
	  }	  
	  
	  if(isset($parms['select_route'])) {
		$form['map-show-route'] = [
		  '#type' => 'button',
		  '#id' => 'map-show-route',
		  '#value' => t('Show only '.$selected_route->getName().' route sites'),    
		]; 
		$form['map-show-all'] = [
		  '#type' => 'button',
		  '#id' => 'map-show-all',
		  '#disabled' => TRUE,
		  '#value' => t('Show all sites'),    
		]; 
	  }	  
	  
	  $form['map'] = ['#markup' => "<div class='sites-map-wrapper'>$map_controls<div class='sites-map' id='map-canvas'></div></div>"];  
	  
	  return $form;
  
  }
  
	function wq_data_map_page() {
		$query = Database::getConnection()
			->select('tblSites')
			->distinct();
		$query->fields('tblSites');
		$query->orderBy('ID', 'ASC');
		$result = $query->execute();
		$site_list = array();
		foreach($result as $site) {
			$site_list[$site->ID] = $site;
		}
		return $site_list;
	}
  
  
  public function submitForm(array &$form, FormStateInterface $form_state){}
  
  
  
	function _wq_data_load_site_routes(&$form_state) {
	  if(!isset($form_state->get('storage')['wq_data'])) {
		  
		$site_list = &drupal_static(__FUNCTION__);
		if (!isset($site_list)) {
		  // generate contents of static variable
		  $query = Database::getConnection()
			->select('tblSites')
			->distinct();
		  $query->fields('tblSites');
		  $query->orderBy('ID', 'ASC');
		  $result = $query->execute();
		  $site_list = array();
		  foreach($result as $site) {
			$site_list[$site->ID] = $site;
		  }
		}
		
		$existing_data = $form_state->get('storage');
		$existing_data["wq_data"]['sites'] = $site_list;		
		$form_state->set('storage', $existing_data);  
		
		// Get the entity type manager service.
		$entity_type_manager = \Drupal::entityTypeManager();

		// Get the storage handler for taxonomy terms.
		$term_storage = $entity_type_manager->getStorage('taxonomy_term');

		// Query for taxonomy terms of the 'routes' bundle.
		$query = $term_storage->getQuery()
		  ->condition('vid', 'routes');

		// Execute the query and retrieve the term IDs.
		$term_ids = $query->execute();

		// Load the taxonomy terms.
		$terms = $term_storage->loadMultiple($term_ids);
		
		$existing_data = $form_state->get('storage');
		$existing_data["wq_data"]['routes'] = $terms;		
		$form_state->set('storage', $existing_data);  
		
		
		
	  }
	}
	
	
		
	/**
	 * Produce basic content to show the map of the basin without site markers
	 * @param  integer $zoom zoom 
	 * @return string        html
	 */
	function wq_data_add_gmap($parms, $sites) {
		
	  $api_key = '';
	  $settings = [];
	  $settings['canvas-id'] = $parms['canvas-id'];
	 /* $settings['center'] = array(
		'lat' => (float) (isset($parms['lat']) ? $parms['lat'] :
			variable_get('wq_data_map_center_lat', '35.65')),
		'lng' => (float) (isset($parms['lng']) ? $parms['lng'] :
			variable_get('wq_data_map_center_lng', '-80.49961609975992')));*/
			
		$settings['center'] = array(
		'lat' => (float) (isset($parms['lat']) ? $parms['lat'] : '35.35933296007971'),
		'lng' => (float) (isset($parms['lng']) ? $parms['lng'] :'-77.8903631701125')
		);	
			
	  $settings['zoom'] = isset($parms['zoom']) ? (int) $parms['zoom'] : 9;
	  $settings['base_dir'] = '/' . drupal_get_path('module', 'wq_data');
	  
		// Get the entity type manager service.
		$entity_type_manager = \Drupal::entityTypeManager();

		// Get the storage handler for taxonomy terms.
		$term_storage = $entity_type_manager->getStorage('taxonomy_term');

		// Query for taxonomy terms of the 'routes' bundle.
		$query = $term_storage->getQuery()
		  ->condition('vid', 'routes');

		// Execute the query and retrieve the term IDs.
		$route_ids = $query->execute();

		// Load the taxonomy terms.
		$route_obj = $term_storage->loadMultiple($route_ids);
		
		$route_objs=[];
		foreach($route_obj as $route_id =>$route) {
			$route_objs[$route->id()]['name'] = $route->getName();
			$route_objs[$route->id()]['field_marker_name'] = "site-route-".strtolower($route->getName());
		}
		
		
		$public_url = file_create_url('public://');
		
		
		if(isset($parms['select_route'])) {
			$settings['selected_route_id'] = $parms['select_route'];
		}
		
		$settings['public_url'] = $public_url;
		$settings['sites'] = $sites;
		
		
		$settings['routes'] = $route_objs;
		
		
		return $settings;
	  
	}
	
	function wq_data_time_to_day($time) {
	  return isset($time) ? date("n/j/Y", strtotime($time)) : '';
	}
  
  

}