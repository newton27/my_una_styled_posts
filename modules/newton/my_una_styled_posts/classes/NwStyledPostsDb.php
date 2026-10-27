<?php defined('BX_DOL') or die('hack attempt');
class NwStyledPostsDb extends BxBaseModTextDb
{
    public function lockBackgroundEntry(int $id)
    {
        $this->query('START TRANSACTION');
        return $this->getRow('SELECT * FROM `nw_styled_posts_entries` WHERE `id`=:id FOR UPDATE',['id'=>$id]);
    }
    public function finishBackgroundEntry(bool $commit): void
    {
        $this->query($commit?'COMMIT':'ROLLBACK');
    }
    public function releasePreviousBackgrounds(int $source): array
    {
        $this->query('START TRANSACTION');
        try {
            $rows=$this->getAll('SELECT `id`,`background_previous` FROM `nw_styled_posts_entries` WHERE `background_file`=:source AND `background_previous`>0 FOR UPDATE',['source'=>$source]);
            $this->query('UPDATE `nw_styled_posts_entries` SET `background_previous`=0 WHERE `background_file`=:source',['source'=>$source]);
            $this->query('COMMIT');
            return array_values(array_filter(array_map(fn($row)=>(int)$row['background_previous'],$rows),fn($id)=>$id!==$source));
        } catch (Throwable $e) { $this->query('ROLLBACK');throw $e; }
    }
    public function pendingEntries(): array
    {
        return $this->getAll("SELECT `id`,`title` FROM `nw_styled_posts_entries` WHERE `status_admin`='pending' ORDER BY `id` DESC LIMIT 100");
    }
    public function recentEntries($profileId = 0, int $before = 0, int $limit = 50): array
    {
        $where=$profileId>0?' AND `author`=:author':'';
        $bindings=$profileId>0?['author'=>(int)$profileId]:[];
        if ($before>0) {$where.=' AND `id`<:before';$bindings['before']=$before;}
        $limit=max(1,min(50,$limit));
        return $this->getAll("SELECT * FROM `nw_styled_posts_entries` WHERE `status`='active' AND `status_admin`='active'".$where." ORDER BY `id` DESC LIMIT ".$limit,$bindings);
    }
}
