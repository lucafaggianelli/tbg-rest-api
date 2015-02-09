<?php

	/**
	 * actions for the api module
	 */
	class apiActions extends TBGAction
	{

		/**
		 * Index page
		 * 
		 * @param TBGRequest $request The incoming request
		 */
		public function runInfo(TBGRequest $request)
        {
            return $this->renderJSON(array(
                'tbgVersion' => TBGSettings::getVersion(),
                'apiVersion' => Api::getModule()->getVersion()
            ));
		}
    
        public function runGetProjects(TBGRequest $request)
        {
            $projects = TBGProject::getAll();
            $return = array();

            foreach ($projects as $p) {
                $return[] = array(
                    'key' => $p->getKey(),
                    'name' => $p->getName()
                );
            }

            return $this->renderJSON($return);
        }
        
        public function runGetProject(TBGRequest $request)
        {
            $key = $request->getParameter('key',null);
            $project = TBGProject::getByKey($key);

            $return = array(
                'key' => $project->getKey(),
                'name' => $project->getName(),
                'description' => $project->getDescription()
            );
            return $this->renderJSON($return);
        }
        
        public function runGetIssues(TBGRequest $request)
        {
            $return = array();
            $key = $request->getParameter('project',null);

            $project = TBGProject::getByKey($key);
            
            if ($project != null) {
                $filters = array('project_id' => array('operator' => '=', 'value' => $project->getId()));
                list ($issues, $count) = TBGIssue::findIssues($filters, 0);
                foreach ($issues as $i) {
                    $assignee = $i->getAssignee();
                    if ($assignee != null)
                        $assignee = $assignee->getUsername();
                    else
                        $assignee = null;

                    $return[] = array(
                        'id' => $i->getId(),
                        'issue_no' => $i->getIssueNo(),
                        'name' => $i->getName(),
                        'title' => $i->getTitle(),
                        'assignee' => $assignee,
                        'completed' => $i->getPercentCompleted(),
                    );
                }
            } else {
                $return = array('error' => 'invalid project key '.$key);
            }

            return $this->renderJSON($return);
        }
        
        public function runGetIssue(TBGRequest $request)
        {
            $return = array();
            $issue = null;
            $key = $request->getParameter('project',null);
            
            try {
                $issue_id = intval($request->getParameter('issue',null));
    		    $issue = TBGContext::factory()->TBGIssue($issue_id);
            } catch (Exception $e) {
                $issue_id = null;
                $return = array('error' => 'Not a numeric ID');
            }

            $project = TBGProject::getByKey($key);

            if ($project != null && $issue != null) {

                $return = array(
                    'id' => $issue->getId(),
                    'issue_no' => $issue->getIssueNo(),
                    'type' => $issue->getIssueType()->getID(),
                    'name' => $issue->getName(),
                    'title' => $issue->getTitle(),
                    'description' => $issue->getDescription(),
                    'reproduction_steps' => $issue->getReproductionSteps(),
                    'completed' => $issue->getPercentCompleted(),
                    'priority' => $issue->getPriority(),
                    'status' => $issue->getStatus(),
                    'state' => $issue->getState(),
                    'comment_count' => $issue->getCommentCount(),
                    'posted_at' => $issue->getPosted(),
                    'updated_at' => $issue->getLastUpdatedTime(),
                    'components' => $issue->getComponents(),
                );

                $return['assignee'] = ($issue->getAssignee() != null) ?
                    $issue->getAssignee()->getUsername() : null;

                $return['posted_by'] = ($issue->getPostedBy() != null) ?
                    $issue->getPostedBy()->getUsername() : null;

            } else {
                $return = array('error' => 'invalid project key '.$key);
            }

            return $this->renderJSON($return);
        }

        public function runGetIssueComments(TBGRequest $request)
        {
            $issue = null;
            $return = array();

            try {
                $issue_id = intval($request->getParameter('issue',null));
    		    $issue = TBGContext::factory()->TBGIssue($issue_id);
            } catch (Exception $e) {
                $return = array('error' => 'Not a numeric ID');
                return;
            }

            foreach($issue->getComments() as $c) {
                $return[] = array(
                    //'title' => $c->getTitle(),
                    'content' => $c->getContent(),
                    'user' => $c->getPostedBy(),
                    'posted_at' => $c->getPosted(),
                    'is_system' => $c->isSystemComment(),
                );
            }

            return $this->renderJSON($return);
        }

        public function runGetUser(TBGRequest $request)
        {
            $return = array();
            $username = $request->getParameter('username',null);

            $user = TBGUser::getByUsername($username);
            
            $return['username'] = $username;
            if ($user != null) {
                $return['avatar'] = $user->getAvatarURL();
                $return['name'] = $user->getDisplayName();
            }

            return $this->renderJSON($return);
        }

        public function runStatistics(TBGRequest $request)
        {
            $return = array();
            $key = $request->getParameter('project',null);
            $type = $request->getParameter('project',null);
            $project = TBGProject::getByKey($key);

            $counts = TBGIssuesTable::getTable()->getStateCountByProjectID($project->getID());
            $return = $counts[0];

            return $this->renderJSON($return);
        }
	}
