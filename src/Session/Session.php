<?php
namespace ZoeEE\Session;

/**
 * Clase para manejar las sesiones del sistema
 *
 * @author Julian Andres Lasso Figueroa <jlasso69@misena.edu.co>
 */
class Session
{

    /**
     * Constructor de la clase Session
     *
     * @param string $name
     *            Nombre de la cookie para manejar la sesión en el cliente
     * @param int $time
     *            Tiempo en segundo de duración de la sesión (3600 = 1 hora)
     */
    public function __construct(string $name, int $time = 3600)
    {
        $this->SetName($name)->Start($time);
    }

    /**
     * Establece el nombre de la cookie para manejar la sesión
     *
     * @param string $name
     *            Nombre de la cookie
     * @return Session
     */
    public function SetName(string $name): Session
    {
        session_name($name);
        return $this;
    }

    public function Has(string $param): bool
    {
        return $_SESSION[$param];
    }

    public function Get(string $param)
    {
        return $_SESSION[$param];
    }

    public function Delete($param): Session
    {
        unset($_SESSION[$param]);
        return $this;
    }

    /**
     * Devuelve el nombre de la cookie que maneja la sesión en el cliente
     *
     * @return string
     */
    public function GetName(): string
    {
        return session_name();
    }

    public function GetId(): string
    {
        return session_id();
    }

    public function SetId(string $id): Session
    {
        session_id($id);
        return $this;
    }

    public function Start($time): Session
    {
        session_start(array(
            'cookie_lifetime' => $time
        ));
        return $this;
    }

    /**
     * Destruye toda la información registrada de una sesión
     *
     * @return Session
     */
    public function Destroy(): Session
    {
        session_destroy();
        return $this;
    }
}
