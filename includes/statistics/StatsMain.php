<?php
/**************************************************************************
**********      English Wikipedia Account Request Interface      **********
***************************************************************************
** Wikipedia Account Request Graphic Design by Charles Melbye,           **
** which is licensed under a Creative Commons                            **
** Attribution-Noncommercial-Share Alike 3.0 United States License.      **
**                                                                       **
** All other code are released under the Public Domain                   **
** by the ACC Development Team.                                          **
**                                                                       **
** See CREDITS for the list of developers.                               **
***************************************************************************/

class StatsMain extends StatisticsPage
{
	function execute()
	{
        global $smarty, $filepath;

		$files = scandir( $filepath . "/includes/statistics/" );

		$statsPageDefinitions = preg_grep("/php$/",$files);
		
        $statsPages = array();
        
		foreach ($statsPageDefinitions as $i) 
        {
			require_once($filepath . "/includes/statistics/" . $i);
			$expld = explode('.', $i);
			$className = $expld[0];
			$statsPageObject = new $className;
            
			if($statsPageObject->hideFromMenu() == false)
			{
			    $statsPages[] = $statsPageObject;
			}
		}
        
		$this->smallStats();
        
        $smarty->assign("statsPages", $statsPages);
        
        $graphList = array("day", "2day", "4day", "week", "2week", "month", "3month");
        $smarty->assign("graphList", $graphList);
       
		return $smarty->fetch("statistics/main.tpl");
	}
	
	function getPageTitle()
	{
		return "Account Creation Statistics";
	}
	
	function getPageName()
	{
		return "Main";
	}
	
	function isProtected()
	{
		return true;
	}
    
	function requiresWikiDatabase()
	{
		return false;
	}
    
    function requiresSimpleHtmlEnvironment()
    {
        return false;   
    }
    
	function hideFromMenu()
	{
		return true;
	}
    
	/**
	 * Gets the relevant statistics from the database for the small statistics table
	 */
	private function smallStats()
	{		
        global $smarty;
        
        $database = gGetDb();
        $requestsQuery = "SELECT COUNT(*) FROM acc_pend WHERE pend_status = :status AND pend_mailconfirm = 'Confirmed';";
        
        $requestsStatement = $database->prepare($requestsQuery);
        
        // TODO: use the request states thing here.
        
        // Open Requests
        $requestsStatement->execute(array(":status" => "Open"));
        $open = $requestsStatement->fetchColumn();
        $requestsStatement->closeCursor();
        $smarty->assign("statsOpen", $open);
        
        // Admin Requests
        $requestsStatement->execute(array(":status" => "Admin"));
        $admin = $requestsStatement->fetchColumn();
        $requestsStatement->closeCursor();
        $smarty->assign("statsAdmin", $admin);
        
        // Checkuser Requests
        $requestsStatement->execute(array(":status" => "Checkuser"));
        $checkuser = $requestsStatement->fetchColumn();
        $requestsStatement->closeCursor();
        $smarty->assign("statsCheckuser", $checkuser);

        // Unconfirmed requests
		$unconfirmedStatement = $database->query("SELECT COUNT(*) FROM acc_pend WHERE pend_mailconfirm != 'Confirmed' AND pend_mailconfirm != '';");
        $unconfirmed = $unconfirmedStatement->fetchColumn();
        $unconfirmedStatement->closeCursor();
        $smarty->assign("statsUnconfirmed", $unconfirmed);
        
        $userStatusStatement = $database->prepare("SELECT COUNT(*) FROM user WHERE status = :status;");
		
        // Admin users
		$userStatusStatement->execute(array(":status" => "Admin"));
        $adminusers = $userStatusStatement->fetchColumn();
        $userStatusStatement->closeCursor();
        $smarty->assign("statsAdminUsers", $adminusers);
        
        // Users
		$userStatusStatement->execute(array(":status" => "User"));
        $users = $userStatusStatement->fetchColumn();
        $userStatusStatement->closeCursor();
        $smarty->assign("statsUsers", $users);
		
        // Suspended users
		$userStatusStatement->execute(array(":status" => "Suspended"));
        $suspendedUsers = $userStatusStatement->fetchColumn();
        $userStatusStatement->closeCursor();
        $smarty->assign("statsSuspendedUsers", $suspendedUsers);
		
        // New users
		$userStatusStatement->execute(array(":status" => "New"));
        $newUsers = $userStatusStatement->fetchColumn();
        $userStatusStatement->closeCursor();
        $smarty->assign("statsNewUsers", $newUsers);
        
		// Most comments on a request
		$mostCommentsStatement = $database->query("SELECT request FROM comment GROUP BY request ORDER BY COUNT(*) DESC LIMIT 1;");
        $mostComments = $mostCommentsStatement->fetchColumn();
        $mostCommentsStatement->closeCursor();
        $smarty->assign("mostComments", $mostComments);
        
        // Welcome queue length
		$welcomeQueueStatement = $database->query("SELECT COUNT(*) FROM acc_welcome WHERE welcome_status = 'Open';");
		$welcomeQueueLength = $welcomeQueueStatement->fetchColumn();
        $welcomeQueueStatement->closeCursor();
        $smarty->assign("welcomeQueueLength",$welcomeQueueLength);
	}
}
