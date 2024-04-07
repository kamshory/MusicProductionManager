<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="article")
 */
class EntityArticle extends MagicObject
{
	/**
	 * Article ID
	 * 
	 * @NotNull
	 * @Column(name="article_id", type="varchar(40)", length=40, nullable=false)
	 * @var string
	 */
	protected $articleId;

    /**
	 * Type
	 * 
	 * @Column(name="type", type="varchar(20)", length=20, nullable=true)
	 * @var string
	 */
	protected $type;

	/**
	 * Title
	 * 
	 * @Column(name="title", type="text", nullable=true)
	 * @var string
	 */
	protected $title;

	/**
	 * Content
	 * 
	 * @Column(name="content", type="longtext", nullable=true)
	 * @var string
	 */
	protected $content;

	/**
	 * Time Create
	 * 
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Time Edit
	 * 
	 * @Column(name="time_edit", type="timestamp", length=19, nullable=true)
	 * @var string
	 */
	protected $timeEdit;

	/**
	 * Admin Create
	 * 
	 * @Column(name="admin_create", type="varchar(40)", length=40, nullable=true, updatable=false)
	 * @var string
	 */
	protected $adminCreate;

    /**
	 * Creator
	 * 
	 * @JoinColumn(name="admin_create")
	 * @var User
	 */
	protected $creator;

	/**
	 * Admin Edit
	 * 
	 * @Column(name="admin_edit", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $adminEdit;

    /**
	 * Editor
	 * 
	 * @JoinColumn(name="admin_edit")
	 * @var User
	 */
	protected $editor;

	/**
	 * IP Create
	 * 
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $ipCreate;

	/**
	 * IP Edit
	 * 
	 * @Column(name="ip_edit", type="varchar(50)", length=50, nullable=true)
	 * @var string
	 */
	protected $ipEdit;

	/**
	 * Draft
	 * 
	 * @Column(name="draft", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var boolean
	 */
	protected $draft;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @var boolean
	 */
	protected $active;

}