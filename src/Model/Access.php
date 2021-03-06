<?php

namespace Model;

/**
 * @Entity @Table(name="acces")
 **/
class Access extends Entity {
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="text") **/
    protected $nom;

    /** @Column(type="integer") **/
    protected $groupe_id;

    /** @Column(type="text") **/
    protected $groupe;

    /** @Column(type="string") **/
    protected $page;

    /** @Column(type="integer") **/
    protected $ordre;

    /** @Column(type="string") **/
    protected $categorie;
}
