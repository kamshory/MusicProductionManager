<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="user_activity")
 */
class UserActivity extends MagicObject
{
	/**
	 * User Activity ID
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="user_activity_id", type="varchar(40)", length=40, nullable=false)
	 * @var string
	 */
	protected $userActivityId;

	/**
	 * Name
	 * @Column(name="name", type="varchar(255)", length=255, nullable=true)
	 * @var string
	 */
	protected $name;

	/**
	 * User ID
	 * @Column(name="user_id", type="varchar(40)", length=40, nullable=true)
	 * @var string
	 */
	protected $userId;

	/**
	 * Path
	 * @Column(name="path", type="text", nullable=true)
	 * @var string
	 */
	protected $path;

	/**
	 * Method
	 * @Column(name="method", type="varchar(10)", length=10, nullable=true)
	 * @var string
	 */
	protected $method;

	/**
	 * Get Data
	 * @Column(name="get_data", type="longtext", nullable=true)
	 * @var string
	 */
	protected $getData;

	/**
	 * Post Data
	 * @Column(name="post_data", type="longtext", nullable=true)
	 * @var string
	 */
	protected $postData;

	/**
	 * Request Body
	 * @Column(name="request_body", type="longtext", nullable=true)
	 * @var string
	 */
	protected $requestBody;

	/**
	 * Time Create
	 * @Column(name="time_create", type="timestamp", length=19, nullable=true, updatable=false)
	 * @var string
	 */
	protected $timeCreate;

	/**
	 * Ip Create
	 * @Column(name="ip_create", type="varchar(50)", length=50, nullable=true, updatable=false)
	 * @var string
	 */
	protected $ipCreate;

}