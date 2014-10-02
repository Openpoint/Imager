SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;



CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;




COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;



CREATE TABLE pages (
    id integer NOT NULL,
    url text,
    image character varying(256),
    title character varying(256),
    sfw boolean,
    biggest integer DEFAULT 0
);


ALTER TABLE public.pages OWNER TO _dbowner_;



CREATE SEQUENCE pages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pages_id_seq OWNER TO _dbowner_;



ALTER SEQUENCE pages_id_seq OWNED BY pages.id;



CREATE TABLE users (
    id integer NOT NULL,
    username text NOT NULL,
    hash text NOT NULL,
    salt text NOT NULL,
    authtoken character varying(50),
    email text NOT NULL,
    role character varying(256) NOT NULL,
    realname character varying(256),
    invitedby character varying(256),
    status character varying(10) DEFAULT 'Invited'::character varying,
    date date
);


ALTER TABLE public.users OWNER TO _dbowner_;



CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO _dbowner_;



ALTER SEQUENCE users_id_seq OWNED BY users.id;




ALTER TABLE ONLY pages ALTER COLUMN id SET DEFAULT nextval('pages_id_seq'::regclass);




ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);




ALTER TABLE ONLY pages
    ADD CONSTRAINT pages_pkey PRIMARY KEY (id);




ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);




REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;




