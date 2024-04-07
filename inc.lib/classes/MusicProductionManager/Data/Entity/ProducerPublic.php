<?php

namespace MusicProductionManager\Data\Entity;

use MagicObject\MagicObject;

/**
 * @Entity
 * @JSON(property-naming-strategy=SNAKE_CASE)
 * @Table(name="producer")
 */
class ProducerPublic extends MagicObject
{
	/**
	 * Producer ID
	 * 
	 * @Id
	 * @GeneratedValue(strategy=GenerationType.UUID)
	 * @NotNull
	 * @Column(name="producer_id", type="varchar(40)", length=40, nullable=false)
	 * @Label(content="Producer ID")
	 * @var string
	 */
	protected $producerId;

	/**
	 * Name
	 * 
	 * @Column(name="name", type="varchar(100)", length=100, nullable=true)
	 * @Label(content="Name")
	 * @var string
	 */
	protected $name;

	/**
	 * Gender
	 * 
	 * @Column(name="gender", type="varchar(2)", length=2, nullable=true)
	 * @Label(content="Gender")
	 * @var string
	 */
	protected $gender;

	/**
	 * Active
	 * 
	 * @Column(name="active", type="tinyint(1)", length=1, default_value="1", nullable=true)
	 * @DefaultColumn(value="1")
	 * @Label(content="Active")
	 * @var boolean
	 */
	protected $active;

}