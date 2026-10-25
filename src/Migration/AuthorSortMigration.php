<?php
namespace Hschottm\LiteratureBundle\Migration;
use Contao\CoreBundle\Migration\{AbstractMigration, MigrationResult};
use Doctrine\DBAL\Connection;

class AuthorSortMigration extends AbstractMigration
{
    public function __construct(private readonly Connection $db) {}
    public function shouldRun(): bool
    {
        if (!$this->db->createSchemaManager()->tablesExist(['tl_literature','tl_literature_author'])) return false;
        $columns = $this->db->createSchemaManager()->listTableColumns('tl_literature');
        if (!isset($columns['authorssort'])) return false;
        return (bool) $this->db->fetchOne("SELECT id FROM tl_literature WHERE authorssort='' AND EXISTS (SELECT 1 FROM tl_literature_author a WHERE a.pid=tl_literature.id AND (a.firstname<>'' OR a.lastname<>'')) LIMIT 1");
    }
    public function run(): MigrationResult
    {
        $this->db->transactional(function (): void {
            foreach ($this->db->fetchFirstColumn("SELECT id FROM tl_literature WHERE authorssort=''") as $id) {
                $sort = '';
                foreach ($this->db->fetchAllAssociative('SELECT firstname,lastname FROM tl_literature_author WHERE pid=? ORDER BY sequence', [$id]) as $person) $sort .= $person['lastname'].','.$person['firstname'];
                if ($sort !== '') $this->db->update('tl_literature', ['authorssort'=>$sort], ['id'=>$id]);
            }
        });
        return $this->createResult(true);
    }
}
