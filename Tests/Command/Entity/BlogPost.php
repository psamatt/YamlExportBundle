<?php

namespace Psamatt\YamlExportBundle\Tests\Command\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="blog_posts")
 * @ORM\Entity
 */
class BlogPost
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string")
     */
    public $title;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $intro;
    
    /**
     * @ORM\Column(type="text")
     */
    public $content;

    /**
     * @ORM\Column(type="datetime")
     */
    public $date_added;

    /**
     * @ORM\Column(type="integer")
     */
    public $category_id;

    /**
     * @ORM\ManyToOne(targetEntity="BlogPostCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    public $category;

}
