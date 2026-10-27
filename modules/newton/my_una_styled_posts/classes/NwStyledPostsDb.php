<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsDb extends BxBaseModTextDb
{
    public function pendingEntries(): array
    {
        return $this->getAll("SELECT `id`,`title` FROM `nw_styled_posts_entries` WHERE `status_admin`='pending' ORDER BY `id` DESC LIMIT 100");
    }
    public function recentEntries($profileId = 0): array
    {
        $where=$profileId>0?' AND `author`=:author':'';
        $bindings=$profileId>0?['author'=>(int)$profileId]:[];
        return $this->getAll("SELECT * FROM `nw_styled_posts_entries` WHERE `status`='active' AND `status_admin`='active'".$where." ORDER BY `id` DESC LIMIT 60",$bindings);
    }
}
