<?php

	/**
	 * Autogenerated module Api
	 *
	 * @author
	 * @version 0.1
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package api
	 * @subpackage core
	 */

	/**
	 * Autogenerated module Api
	 *
	 * @package api
	 * @subpackage core
	 * 
	 * @Table(name="TBGModulesTable")
	 */
	class Api extends TBGModule
	{

		protected $_longname = 'Api';
		
		protected $_description = 'Autogenerated module Api';
		
		protected $_module_config_title = 'Api';
		
		protected $_module_config_description = 'Set up the Api module from this section';
		
		protected $_has_config_settings = true;
		
        protected $_module_version = '0.1';

        const API_BASE = '/api-rest';

		/**
		 * Return an instance of this module
		 * Convenience method to provide better code completion
		 *
		 * @return Api
		 */
		public static function getModule()
		{
			return TBGContext::getModule('api');
		}

		protected function _initialize()
		{
		}

		protected function _addListeners()
		{
		}

		protected function _addRoutes()
		{
            $this->addRoute('apix',             self::API_BASE,                     'info');

			$this->addRoute('apix_projects',    self::API_BASE.'/projects',         'getProjects');
            $this->addRoute('apix_project',     self::API_BASE.'/projects/:key',    'getProject');

			$this->addRoute('apix_issues',      self::API_BASE.'/projects/:project/issues',         'getIssues');
			$this->addRoute('apix_issue',       self::API_BASE.'/projects/:project/issues/:issue',  'getIssue');
			$this->addRoute('apix_comments',    self::API_BASE.'/projects/:project/issues/:issue/comments',         'getIssueComments');

			$this->addRoute('apix_users',       self::API_BASE.'/users/:username',  'getUser');

			$this->addRoute('apix_statistics',  self::API_BASE.'/projects/:project/statistics/:type',  'statistics');
		}

		protected function _install($scope)
		{
		}

		protected function _loadFixtures($scope)
		{
		}

		protected function _uninstall()
		{
		}

		public function getRoute()
		{
			return TBGContext::getRouting()->generate('api_index');
		}

	}
