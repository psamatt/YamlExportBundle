<?php

namespace Psamatt\YamlExportBundle\Tests\Command\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blog_post_categories")
 * @ORM\Entity
 */
class BlogPostCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     */
    public $name;

}
