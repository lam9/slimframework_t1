<?php
/**
 * Created by PhpStorm.
 * User: lmo
 * Date: 01.11.17
 * Time: 18:29
 */

class TicketMapper
{
    private $tickets;

    function __construct(PDO $db)
    {
        var_dump($db);
        // charge les tickets. Simmule accès db.
        $this->getTickets();

    }


    function getTickets()
    {
        $this->tickets=[];
        $this->tickets[]=['id' => 1, 'title'=> 'titre1', 'component'=>'composant1', 'description'=> 'desc1', 'action' => 'act1'];
        $this->tickets[]=['id' => 2, 'title'=> 'titre2', 'component'=>'composant2', 'description'=> 'desc2', 'action' => 'act2'];
        return $this->tickets;
    }

    function getTicketById($ticket_id)
    {
        // Recherche le ticket dans les tickets existants.
        foreach ($this->tickets as $ticket )
        {
            if( $ticket['id'] === $ticket_id)
                return $ticket;
        }
        // Si le ticket n'existe pas, renvoie null.
        return null;
    }

}