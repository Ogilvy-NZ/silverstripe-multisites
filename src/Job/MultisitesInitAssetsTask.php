<?php

namespace Symbiote\Multisites\Job;

use SilverStripe\Assets\Folder;
use SilverStripe\Assets\File;
use SilverStripe\Dev\BuildTask;
use Symbiote\Multisites\Multisites;

/**
 * move assets to a folder with the name of the current Multisite->Host
 *
 * @package default
 * @author
 * */
class MultisitesInitAssetsTask extends BuildTask
{
    private static $segment = 'MultisitesInitAssetsTask';
    protected $enabled      = true;
    protected $description  = 'move assets to a folder with the name of the current multisite';

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        $currentSite = Multisites::inst()->getCurrentSite();
        $folderName  = $currentSite->Host ?: "site-$currentSite->ID";
        $folder      = Folder::find_or_make($folderName);

        $files = File::get()->filter('ParentID', array(0))->exclude('ID', $folder->ID);

        if (!$files->count()) {
            return;
        }

        foreach ($files as $file) {
            if (file_exists($file->getFullPath())) {
                $file->ParentID = $folder->ID;
                $file->write();
                echo $file->Filename.' moved <br />';
            }
        }
    }
}
