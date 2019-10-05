select * from bel_terminal where id_tipo = 1

update bel_terminal set
  botao_ativar = 8
  , botao_desativar = 5
where id_tipo = 1  

select * from bel_mensagem

select * from bel_chamado

select * from bel_monitoramento

select * from bel_monitoramento_vw where id_tipo = 1


drop view bel_monitoramento_vw;

CREATE OR REPLACE VIEW public.bel_monitoramento_vw
AS SELECT m.cliente_id,
    ( SELECT d.nome
           FROM bel_cliente d
          WHERE d.id_cliente = m.cliente_id) AS cliente,
    m.unidade_id,
    ( SELECT d.nome
           FROM bel_unidade d
          WHERE d.id_unidade = m.unidade_id) AS unidade,
    m.estacao_id,
    ( SELECT d.nome
           FROM bel_estacao d
          WHERE d.id_estacao = m.estacao_id) AS estacao,
    m.terminal_id,
    t.nome AS terminal,
    m.ult_botao,
        CASE
            WHEN m.ult_botao::text = t.botao_ativar::text THEN t.botao_ativar_cor
            ELSE t.botao_desativar_cor
        END AS cor_fundo,
    t.terminal_nr,
    m.ult_botao_ts
    , t.id_tipo
   FROM bel_terminal t,
    bel_monitoramento m
  WHERE t.id_terminal = m.terminal_id;

-- Permissions

ALTER TABLE public.bel_monitoramento_vw OWNER TO delta;
GRANT ALL ON TABLE public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cliente_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cliente) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(unidade_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(unidade) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(estacao_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(estacao) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(ult_botao) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cor_fundo) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal_nr) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(ult_botao_ts) ON public.bel_monitoramento_vw TO delta;



select * from bel_terminal where terminal_nr = '1'

select * from bel_mensagem where terminal_id = 200

select * from bel_monitoramento_vw where id_tipo = 1


drop function bel_terminal_add_f( varchar, bigint );

CREATE OR REPLACE FUNCTION public.bel_terminal_add_f(nome_p character varying, id_cliente_p bigint, id_tipo_p bigint )
 RETURNS bigint
 LANGUAGE plpgsql
AS $function$
declare
  terminal_r    bel_terminal%rowtype;
begin
	
	select * into terminal_r
	from bel_terminal
	where terminal_nr = nome_p
	  and id_cliente = id_cliente_p;
	 
	if( not found ) then
	
	  select nextval('bel_terminal_seq') into terminal_r.id_terminal;
	 
	  insert into bel_terminal( id_terminal, id_cliente, nome, terminal_nr, id_tipo )
	  values( terminal_r.id_terminal, id_cliente_p, nome_p, nome_p, id_tipo_p );
	 
	end if;
	
	
  return terminal_r.id_terminal;	
end; $function$






CREATE OR REPLACE FUNCTION public.bel_mensagem_bif()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare
  retorno int8      := 0;
  linha_v int8      := 0;
  atualizado_v int8 := 0;
  a    record; -- cursor
begin
	new.cliente_id := bel_cliente_add_f( new.cliente );
	new.unidade_id := bel_unidade_add_f( new.unidade, new.cliente_id );
  new.estacao_id := bel_estacao_add_f( new.estacao, new.cliente_id );
	new.terminal_id := bel_terminal_add_f( new.terminal_name, new.cliente_id, new.terminal_tipo );
	
	update bel_terminal set
	  ult_botao  = new.terminal_botao 
	  , ult_ts = current_timestamp
	where id_terminal = new.terminal_id;

  select  atualizar_botao_f() into retorno;	

---------------------------------------------------------------------------
--                        CHAMADO                                      ----
---------------------------------------------------------------------------
linha_v := 0;
atualizado_v := 0;

for a in ( select id_chamado
from bel_chamado 
where id_cliente = new.cliente_id  
  and  id_terminal = new.terminal_id
  and dt_atendimento is null
  order by id_chamado desc )
loop
  linha_v := linha_v + 1;
  if( linha_v = 1 ) then
    update bel_chamado set
      ts_atendimento = new.terminal_ts
      , dt_atendimento = cast( new.terminal_ts as date )
    where id_chamado = a.id_chamado;
    atualizado_v := 1;
  end if;
end loop;

if( atualizado_v = 0 ) then
  INSERT INTO bel_chamado(
    id_chamado, id_cliente, id_terminal
    , ts_abertura
    , dt_abertura
    ,  terminal_motivo)
  VALUES (
      nextval('bel_chamado_seq'), new.cliente_id, new.terminal_id
      , new.terminal_ts 
      , cast( new.terminal_ts as date )
      , new.terminal_motivo );
end if;





---------------------------------------------------------------------------
--                        MONITORAMENTO                                ----
---------------------------------------------------------------------------


  
 
   -- Atualizar Monitoramento
   
	
		update bel_monitoramento set
		  ult_botao = new.terminal_botao
		  , ult_botao_ts = new.terminal_ts
		  , ult_botao_dt = cast( new.terminal_ts as date )
		where cliente_id = new.cliente_id  
		  and unidade_id = new.unidade_id
		  and estacao_id = new.estacao_id
		  and terminal_id = new.terminal_id;
		 
	  if( not found ) then
	    insert into bel_monitoramento(
	      cliente_id, unidade_id, estacao_id
	      , terminal_id, ult_botao
	      , ult_botao_ts, ult_botao_dt )
	    values(
	      new.cliente_id, new.unidade_id
	      , new.estacao_id
	      , new.terminal_id
	      , new.terminal_botao, new.inclusao_ts
	      , cast( new.inclusao_ts as date ) 
	      )  ;
	  end if;
 
	
	return new;
end;$function$

select
'update bel_terminal set id_tipo = '||terminal_tipo||' where id_terminal = '||terminal_id||';' as comando
from ( 
select distinct terminal_id, terminal_tipo
from bel_mensagem
order by 1 ) a

select * from bel_terminal where terminal_nr = '876704'
select * from bel_terminal where id_tipo = 1


create table bel_prioridades(
  id_cliente int8 not null,
  ini interval,
  fim interval,
  cor varchar(30)
);

delete from bel_prioridades;

insert into bel_prioridades( id_cliente, ini, fim, cor )
select id_cliente
, cast( '00:00:00' as interval )
, cast( '00:05:00' as interval )
, '#1cc88a'
from bel_cliente;

insert into bel_prioridades( id_cliente, ini, fim, cor )
select id_cliente
, cast( '00:05:01' as interval )
, cast( '00:10:00' as interval )
, '#f6c23e'
from bel_cliente;

insert into bel_prioridades( id_cliente, ini, fim, cor )
select id_cliente
, cast( '00:10:01' as interval )
, cast( '23:59:59' as interval )
, '#e74a3b'
from bel_cliente;


select * from bel_prioridades

select t.nome
, ( select count(1) from bel_chamado d 
    where d.id_cliente = m.cliente_id 
      and d.id_terminal = m.terminal_id
      and d.dt_atendimento is null ) as chamados
, m.* 
from
  bel_terminal t,
  bel_monitoramento m 
where ( t.id_terminal = m.terminal_id )  

select * from bel_chamado



create or replace  function bel_terminal_get_status( id_cliente_p int8, id_terminal_p int8 ) returns varchar as $$
/*
 
  select t.nome
   , bel_terminal_get_status( t.id_cliente, t.id_terminal ) as status
  from bel_terminal t
  where id_tipo = 1
  
  select 'update bel_chamado set dt_atendimento = null, ts_atendimento = null, intervalo_tm = null where id_chamado = '||id||';' as comando
  from (
  select id_terminal,max( id_chamado ) as id
  from bel_chamado
  group by id_terminal ) x
  
  select * from bel_chamado
  
  select * from bel_prioridades
  
 */
declare
  retorno_v  varchar;
  chamado_r  bel_chamado%rowtype;
begin
	retorno_v := '#858796'; -- ''bg-secondary';
	
	-- levantar o chamado aberto a mais tempo
	
	select min( id_chamado ) into chamado_r.id_chamado
	from bel_chamado 
	where id_cliente = id_cliente_p
	  and id_terminal = id_terminal_p
	  and dt_atendimento is null;
	
	select * into chamado_r
	from bel_chamado
	where id_chamado = chamado_r.id_chamado;
	
	if( found ) then
	
    select cor into retorno_v
    from bel_prioridades
    where id_cliente = id_cliente_p
      and ( current_timestamp - chamado_r.ts_abertura ) between ini and fim;
     
    if( not found ) then
      retorno_v := '#858796'; -- ''bg-secondary';
	  end if;
	end if;
	
	
	return retorno_v;
	
end;$$ language plpgsql;









select * from bel_terminal where terminal_nr = '11828533'

select 
 ( select d.nome from bel_terminal d where d.id_terminal = c.id_terminal ) as nome
, to_char( current_timestamp - ts_abertura, 'hh24:mi:ss' )  as intervalo
,bel_terminal_get_status( id_cliente, id_terminal ) as cor 
from bel_chamado c
where id_terminal = 223



select *
from bel_chamado
where id_terminal = 223

update bel_chamado set ts_abertura = current_timestamp  where id_terminal = 223

select nome
, id_cliente
, id_terminal
,bel_terminal_get_status( id_cliente, id_terminal ) as cor 
from bel_terminal c
where id_terminal = 223


		select terminal, terminal_id
         , cor_fundo, terminal_nr
         , ult_botao_ts
         , bel_terminal_get_status( cliente_id, terminal_id ) as cor_status
		from bel_monitoramento_vw
		where cliente_id = 2
	--	  and estacao_id = " . $e->id . "
		order by terminal

select terminal, terminal_id , cor_fundo, terminal_nr 
, ult_botao_ts , bel_terminal_get_status( cliente_id, terminal_id ) as cor_status from bel_monitoramento_vw where cliente_id = 2 order by terminal





select * from bel_terminal where id_tipo = 1

update bel_terminal set
  botao_ativar = 8
  , botao_desativar = 5
where id_tipo = 1  

select * from bel_mensagem

select * from bel_chamado

select * from bel_monitoramento

select * from bel_monitoramento_vw where id_tipo = 1


drop view bel_monitoramento_vw;

CREATE OR REPLACE VIEW public.bel_monitoramento_vw
AS SELECT m.cliente_id,
    ( SELECT d.nome
           FROM bel_cliente d
          WHERE d.id_cliente = m.cliente_id) AS cliente,
    m.unidade_id,
    ( SELECT d.nome
           FROM bel_unidade d
          WHERE d.id_unidade = m.unidade_id) AS unidade,
    m.estacao_id,
    ( SELECT d.nome
           FROM bel_estacao d
          WHERE d.id_estacao = m.estacao_id) AS estacao,
    m.terminal_id,
    t.nome AS terminal,
    m.ult_botao,
        CASE
            WHEN m.ult_botao::text = t.botao_ativar::text THEN t.botao_ativar_cor
            ELSE t.botao_desativar_cor
        END AS cor_fundo,
    t.terminal_nr,
    m.ult_botao_ts
    , t.id_tipo
   FROM bel_terminal t,
    bel_monitoramento m
  WHERE t.id_terminal = m.terminal_id;

-- Permissions

ALTER TABLE public.bel_monitoramento_vw OWNER TO delta;
GRANT ALL ON TABLE public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cliente_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cliente) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(unidade_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(unidade) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(estacao_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(estacao) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(ult_botao) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cor_fundo) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal_nr) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(ult_botao_ts) ON public.bel_monitoramento_vw TO delta;



select * from bel_terminal where terminal_nr = '1'

select * from bel_mensagem where terminal_id = 200

select * from bel_monitoramento_vw where id_tipo = 1


drop function bel_terminal_add_f( varchar, bigint );

CREATE OR REPLACE FUNCTION public.bel_terminal_add_f(nome_p character varying, id_cliente_p bigint, id_tipo_p bigint )
 RETURNS bigint
 LANGUAGE plpgsql
AS $function$
declare
  terminal_r    bel_terminal%rowtype;
begin
	
	select * into terminal_r
	from bel_terminal
	where terminal_nr = nome_p
	  and id_cliente = id_cliente_p;
	 
	if( not found ) then
	
	  select nextval('bel_terminal_seq') into terminal_r.id_terminal;
	 
	  insert into bel_terminal( id_terminal, id_cliente, nome, terminal_nr, id_tipo )
	  values( terminal_r.id_terminal, id_cliente_p, nome_p, nome_p, id_tipo_p );
	 
	end if;
	
	
  return terminal_r.id_terminal;	
end; $function$






CREATE OR REPLACE FUNCTION public.bel_mensagem_bif()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare
  retorno int8      := 0;
  linha_v int8      := 0;
  atualizado_v int8 := 0;
  a    record; -- cursor
begin
	new.cliente_id := bel_cliente_add_f( new.cliente );
	new.unidade_id := bel_unidade_add_f( new.unidade, new.cliente_id );
  new.estacao_id := bel_estacao_add_f( new.estacao, new.cliente_id );
	new.terminal_id := bel_terminal_add_f( new.terminal_name, new.cliente_id, new.terminal_tipo );
	
	update bel_terminal set
	  ult_botao  = new.terminal_botao 
	  , ult_ts = current_timestamp
	where id_terminal = new.terminal_id;

  select  atualizar_botao_f() into retorno;	

---------------------------------------------------------------------------
--                        CHAMADO                                      ----
---------------------------------------------------------------------------
linha_v := 0;
atualizado_v := 0;

for a in ( select id_chamado
from bel_chamado 
where id_cliente = new.cliente_id  
  and  id_terminal = new.terminal_id
  and dt_atendimento is null
  order by id_chamado desc )
loop
  linha_v := linha_v + 1;
  if( linha_v = 1 ) then
    update bel_chamado set
      ts_atendimento = new.terminal_ts
      , dt_atendimento = cast( new.terminal_ts as date )
    where id_chamado = a.id_chamado;
    atualizado_v := 1;
  end if;
end loop;

if( atualizado_v = 0 ) then
  INSERT INTO bel_chamado(
    id_chamado, id_cliente, id_terminal
    , ts_abertura
    , dt_abertura
    ,  terminal_motivo)
  VALUES (
      nextval('bel_chamado_seq'), new.cliente_id, new.terminal_id
      , new.terminal_ts 
      , cast( new.terminal_ts as date )
      , new.terminal_motivo );
end if;





---------------------------------------------------------------------------
--                        MONITORAMENTO                                ----
---------------------------------------------------------------------------


  
 
   -- Atualizar Monitoramento
   
	
		update bel_monitoramento set
		  ult_botao = new.terminal_botao
		  , ult_botao_ts = new.terminal_ts
		  , ult_botao_dt = cast( new.terminal_ts as date )
		where cliente_id = new.cliente_id  
		  and unidade_id = new.unidade_id
		  and estacao_id = new.estacao_id
		  and terminal_id = new.terminal_id;
		 
	  if( not found ) then
	    insert into bel_monitoramento(
	      cliente_id, unidade_id, estacao_id
	      , terminal_id, ult_botao
	      , ult_botao_ts, ult_botao_dt )
	    values(
	      new.cliente_id, new.unidade_id
	      , new.estacao_id
	      , new.terminal_id
	      , new.terminal_botao, new.inclusao_ts
	      , cast( new.inclusao_ts as date ) 
	      )  ;
	  end if;
 
	
	return new;
end;$function$

select
'update bel_terminal set id_tipo = '||terminal_tipo||' where id_terminal = '||terminal_id||';' as comando
from ( 
select distinct terminal_id, terminal_tipo
from bel_mensagem
order by 1 ) a

select * from bel_terminal where terminal_nr = '876704'
select * from bel_terminal where id_tipo = 1


create table bel_prioridades(
  id_cliente int8 not null,
  ini interval,
  fim interval,
  cor varchar(30)
);

delete from bel_prioridades;

insert into bel_prioridades( id_cliente, ini, fim, cor )
select id_cliente
, cast( '00:00:00' as interval )
, cast( '00:05:00' as interval )
, '#1cc88a'
from bel_cliente;

insert into bel_prioridades( id_cliente, ini, fim, cor )
select id_cliente
, cast( '00:05:01' as interval )
, cast( '00:10:00' as interval )
, '#f6c23e'
from bel_cliente;

insert into bel_prioridades( id_cliente, ini, fim, cor )
select id_cliente
, cast( '00:10:01' as interval )
, cast( '23:59:59' as interval )
, '#e74a3b'
from bel_cliente;




select t.nome
, ( select count(1) from bel_chamado d 
    where d.id_cliente = m.cliente_id 
      and d.id_terminal = m.terminal_id
      and d.dt_atendimento is null ) as chamados
, m.* 
from
  bel_terminal t,
  bel_monitoramento m 
where ( t.id_terminal = m.terminal_id )  

select * from bel_chamado



create or replace  function bel_terminal_get_status( id_cliente_p int8, id_terminal_p int8 ) returns varchar as $$
/*
 
  
 */
declare
  retorno_v  varchar;
  chamado_r  bel_chamado%rowtype;
 idv      int8;
 a record;
 intervalo_v  interval;
begin
	retorno_v := '#858796'; -- ''bg-secondary';
	
	for a in (
	  select *
	  from bel_chamado
	  where id_cliente = id_cliente_p
	    and id_terminal = id_terminal_p
	    and dt_atendimento is null
	  order by id_chamado desc
	) loop
	  if( ( current_timestamp - a.ts_abertura ) <= '00:01:00'::interval ) then
	    return '#00ff00'; -- '#1CC88A';
	  else
	    return '#ff0000';
	  end if;
	end loop;

	return retorno_v;

	
end;$$ language plpgsql;









select * from bel_terminal where terminal_nr = '11828533'

select 
 ( select d.nome from bel_terminal d where d.id_terminal = c.id_terminal ) as nome
, to_char( current_timestamp - ts_abertura, 'hh24:mi:ss' )  as intervalo
,bel_terminal_get_status( id_cliente, id_terminal ) as cor 
from bel_chamado c
where id_terminal = 223



select *
from bel_chamado
where id_terminal = 223

select nome
, id_cliente
, id_terminal
,bel_terminal_get_status( id_cliente, id_terminal ) as cor 
from bel_terminal c
where id_terminal = 223


		select terminal, terminal_id
         , cor_fundo, terminal_nr
         , ult_botao_ts
         , bel_terminal_get_status( cliente_id, terminal_id ) as cor_status
		from bel_monitoramento_vw
		where cliente_id = 2
	--	  and estacao_id = " . $e->id . "
		order by terminal





select to_char( ini, 'hh24:mi:ss') as inicio
,  to_char( fim, 'hh24:mi:ss') as fim
, cor
from bel_prioridades
where ( current_timestamp - ( current_timestamp - cast( '00:00:15' as interval ) ) ) between ini and fim


-- trabalhar faixas amarela e vermelha a partir do inicio de tempo.




select ts_abertura
, current_timestamp - ts_abertura as intervalo
from bel_chamado
where id_terminal = 225
  and dt_atendimento is null


update bel_chamado set 
ts_abertura =   ( ( current_timestamp - cast( '00:00:15' as interval ) ) )::timestamp
where id_terminal = 225
  and dt_atendimento is null

select to_char( current_timestamp - ts_abertura, 'hh24:mi:ss' ) as tempo
, bel_terminal_get_status( id_cliente, id_terminal ) as cor_status 
, id_chamado
from bel_chamado
where id_terminal = 223
  and dt_atendimento is null

-- Abrir o Chamado  
update bel_chamado set
  dt_atendimento = null
  , ts_abertura = current_timestamp -- - '00:02:00'::interval
where id_chamado = 52156




-- Fechar o Chamado
update bel_chamado set
  dt_atendimento = current_date
where id_chamado = 52156



select current_timestamp, ts_abertura
, current_timestamp - ts_abertura as intervalo01
, id_cliente, id_terminal
from bel_chamado where id_chamado = 52156

select to_char( current_timestamp - ts_abertura, 'hh24:mi:ss' ) --into retorno_v
from bel_chamado
where id_cliente = 2
  and id_terminal = 223
  and dt_atendimento is null

create or replace function bel_chamado_aberto_f( id_cliente_p int8, id_terminal_p int8 ) returns varchar as $$
declare
  retorno_v  varchar;
begin
	
select cast( current_timestamp - ts_abertura as varchar ) into strict retorno_v
from bel_chamado
where id_cliente = id_cliente_p
  and id_terminal = id_terminal_p
  and dt_atendimento is null;

  
  return  retorno_v;
EXCEPTION
        WHEN NO_DATA_FOUND THEN
            return '00:00:00';

end;$$ language plpgsql;


select terminal, terminal_id , cor_fundo, terminal_nr 
, ult_botao_ts
, bel_terminal_get_status( cliente_id, terminal_id ) as cor_status 
, bel_chamado_aberto_f( cliente_id, terminal_id ) as tm_status 
from bel_monitoramento_vw 
where cliente_id = 2 and terminal_id = 223 





-------- bloquear adicao de terminais


CREATE OR REPLACE FUNCTION public.bel_estacao_regra01f( id_cliente_p bigint, id_estacao_p int8 )
 RETURNS integer
 LANGUAGE plpgsql
AS $function$
declare
  estacao_r   bel_estacao%rowtype;
begin
	select * into estacao_r
	from bel_estacao
	where id_cliente = id_cliente_p
	  and id_estacao = id_estacao_p;

  if( estacao_r.fl_add_term = 'S' ) then
    return 1;
  end if;	
  return 0;
end;$function$





---------------------------------------------------------------------------
--                        TRIGGER DE MENSAGEM                          ----
---------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION public.bel_mensagem_bif()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
declare
  retorno int8      := 0;
  linha_v int8      := 0;
  atualizado_v int8 := 0;
  a    record; -- cursor
begin
	new.cliente_id := bel_cliente_add_f( new.cliente );
	new.unidade_id := bel_unidade_add_f( new.unidade, new.cliente_id );
  new.estacao_id := bel_estacao_add_f( new.estacao, new.cliente_id );
  
  -- Estacao não permite incluir novos terminais
  --
  if( bel_estacao_regra01f( new.cliente_id, new.estacao_id ) = 0 ) then
    return new;
  end if;
  

  
	new.terminal_id := bel_terminal_add_f( new.terminal_name, new.cliente_id, new.terminal_tipo );
	
	update bel_terminal set
	  ult_botao  = new.terminal_botao 
	  , ult_ts = current_timestamp
	where id_terminal = new.terminal_id;

  select  atualizar_botao_f() into retorno;	

---------------------------------------------------------------------------
--                        CHAMADO                                      ----
---------------------------------------------------------------------------
linha_v := 0;
atualizado_v := 0;

for a in ( select id_chamado
from bel_chamado 
where id_cliente = new.cliente_id  
  and  id_terminal = new.terminal_id
  and dt_atendimento is null
  order by id_chamado desc )
loop
  linha_v := linha_v + 1;
  if( linha_v = 1 ) then
    update bel_chamado set
      ts_atendimento = new.terminal_ts
      , dt_atendimento = cast( new.terminal_ts as date )
    where id_chamado = a.id_chamado;
    atualizado_v := 1;
  end if;
end loop;

if( atualizado_v = 0 ) then
  INSERT INTO bel_chamado(
    id_chamado, id_cliente, id_terminal
    , ts_abertura
    , dt_abertura
    ,  terminal_motivo)
  VALUES (
      nextval('bel_chamado_seq'), new.cliente_id, new.terminal_id
      , new.terminal_ts 
      , cast( new.terminal_ts as date )
      , new.terminal_motivo );
end if;





---------------------------------------------------------------------------
--                        MONITORAMENTO                                ----
---------------------------------------------------------------------------


  
 
   -- Atualizar Monitoramento
   
	
		update bel_monitoramento set
		  ult_botao = new.terminal_botao
		  , ult_botao_ts = new.terminal_ts
		  , ult_botao_dt = cast( new.terminal_ts as date )
		where cliente_id = new.cliente_id  
		  and unidade_id = new.unidade_id
		  and estacao_id = new.estacao_id
		  and terminal_id = new.terminal_id;
		 
	  if( not found ) then
	    insert into bel_monitoramento(
	      cliente_id, unidade_id, estacao_id
	      , terminal_id, ult_botao
	      , ult_botao_ts, ult_botao_dt )
	    values(
	      new.cliente_id, new.unidade_id
	      , new.estacao_id
	      , new.terminal_id
	      , new.terminal_botao, new.inclusao_ts
	      , cast( new.inclusao_ts as date ) 
	      )  ;
	  end if;
 
	
	return new;
end;$function$




create table bel_usuario_unidade(
  id_usuario_unidade int8 not null,
  id_cliente int8,
  id_usuario int8,
  id_unidade int8,
  constraint bel_usuario_unidade_pk primary key( id_usuario_unidade )
  , constraint id_usuario_unidade_ak01 unique( id_cliente, id_usuario, id_unidade )
  );
 
create sequence id_usuario_unidade_seq start 1 cache 1;

create index bel_usuario_unidade_fk01 on bel_usuario_unidade( id_cliente ); 
create index bel_usuario_unidade_fk02 on bel_usuario_unidade( id_usuario ); 
create index bel_usuario_unidade_fk03 on bel_usuario_unidade( id_unidade ); 
 
















update bel_chamado set
  dt_atendimento = cast( ts_abertura as date )
  , ts_atendimento = ts_abertura + '00:00:45'::interval
where dt_atendimento is null;



update bel_chamado c set
  dt_atendimento = null
  , ts_atendimento = null
  , ts_abertura = current_timestamp - cast( '00:00:40' as interval )
  , dt_abertura = cast( current_timestamp as date )
where id_chamado = (
select max( id_chamado ) from bel_chamado where id_terminal = c.id_terminal )


	  select current_timestamp, ts_abertura
	  , current_timestamp - ts_abertura as dif
	  , case 
	      when current_timestamp - ts_abertura between '00:00:00'::interval and '00:01:00'::interval then '#00ff00'
	      when current_timestamp - ts_abertura between '00:01:01'::interval and '00:10:00'::interval then '#ffff00'
	      else '#0000ff'
	    end as cor
	   , bel_terminal_get_status( id_cliente, id_terminal ) as tm
	   , id_cliente, id_terminal 
	  from bel_chamado
where id_chamado = (
select max( id_chamado ) from bel_chamado where id_terminal = 222 and dt_atendimento is null )

select *
	  from bel_chamado
where id_chamado = (
select max( id_chamado ) from bel_chamado where id_terminal = 222 and dt_atendimento is null )


		select terminal, terminal_id
         , cor_fundo, terminal_nr
         , ult_botao_ts
         , bel_terminal_get_status( cliente_id, terminal_id ) as cor_status
        , segundos
		from bel_monitoramento_vw m
		where cliente_id = 2
		  and estacao_id = 27
		order by terminal
		
	    
	  order by id_chamado desc		
	  
	select * from bel_estacao  


select 
 cast( age( current_timestamp,   ts_abertura ) as varchar ) as dif2
, substr( cast( age( current_timestamp,   ts_abertura ) as varchar ), 1, 2)::integer * 60 * 60 as hora
, substr( cast( age( current_timestamp,   ts_abertura ) as varchar ), 4, 2)::integer * 60 as min
, substr( cast( age( current_timestamp,   ts_abertura ) as varchar ), 7, 2) as seg
, current_timestamp as ref, ts_abertura
, current_timestamp - ts_abertura as dif
from bel_chamado
where id_chamado = (
select max( id_chamado ) from bel_chamado where id_terminal = 219 )

	
	select to_char( ( current_timestamp - ts_abertura ), 'hh24:mi:ss') as dd
	, bel_chamado_aberto_seg_f( id_cliente, id_terminal ) as dif
	from bel_chamado   
	where dt_atendimento is null and id_terminal = 219


CREATE OR REPLACE FUNCTION public.bel_chamado_aberto_seg_f(id_cliente_p bigint, id_terminal_p bigint)
 RETURNS integer
 LANGUAGE plpgsql
AS $function$
declare
  retorno_v  integer := 0;
  hr_v integer;
  min_v integer;
 seg_v integer;
begin
	
select 
 cast( substr( cast( age( current_timestamp,   ts_abertura ) as varchar ), 1, 2) as integer ) * 60 * 60 
, cast( substr( cast( age( current_timestamp,   ts_abertura ) as varchar ), 4, 2) as integer ) * 60 
, cast( substr( cast( age( current_timestamp,   ts_abertura ) as varchar ), 7, 2) as integer ) as seg
     into strict hr_v, min_v, seg_v
from bel_chamado
where id_cliente = id_cliente_p
  and id_terminal = id_terminal_p
  and dt_atendimento is null;

 if( not found ) then
             return 0;
 end if;
  
  return  seg_v;
 
end;$function$

		select terminal, terminal_id
         , cor_fundo, terminal_nr
         , ult_botao_ts
         , bel_terminal_get_status( cliente_id, terminal_id ) as cor_status
         , bel_chamado_aberto_f( cliente_id, terminal_id ) as tm_status 
		from bel_monitoramento_vw m
		where terminal_id = 219
		
		where cliente_id = ?
		  and estacao_id = ?
		order by terminal

	
drop VIEW public.bel_monitoramento_vw;	
	
	CREATE OR REPLACE VIEW public.bel_monitoramento_vw
AS SELECT m.cliente_id,
    ( SELECT d.nome
           FROM bel_cliente d
          WHERE d.id_cliente = m.cliente_id) AS cliente,
    m.unidade_id,
    ( SELECT d.nome
           FROM bel_unidade d
          WHERE d.id_unidade = m.unidade_id) AS unidade,
    m.estacao_id,
    ( SELECT d.nome
           FROM bel_estacao d
          WHERE d.id_estacao = m.estacao_id) AS estacao,
    m.terminal_id,
    t.nome AS terminal,
    m.ult_botao,
        CASE
            WHEN m.ult_botao::text = t.botao_ativar::text THEN t.botao_ativar_cor
            ELSE t.botao_desativar_cor
        END AS cor_fundo,
    t.terminal_nr,
    m.ult_botao_ts,
    t.id_tipo
	, 0 as segundos
   FROM bel_terminal t,
    bel_monitoramento m
  WHERE t.id_terminal = m.terminal_id;

-- Permissions

ALTER TABLE public.bel_monitoramento_vw OWNER TO delta;
GRANT ALL ON TABLE public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cliente_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cliente) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(unidade_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(unidade) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(estacao_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(estacao) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal_id) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(ult_botao) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(cor_fundo) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(terminal_nr) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(ult_botao_ts) ON public.bel_monitoramento_vw TO delta;
GRANT ALL, SELECT, INSERT, UPDATE, DELETE, REFERENCES(id_tipo) ON public.bel_monitoramento_vw TO delta;




CREATE OR REPLACE FUNCTION public.bel_terminal_get_status(id_cliente_p bigint, id_terminal_p bigint)
 RETURNS character varying
 LANGUAGE plpgsql
AS $function$
/*
 
  
 */
declare
  retorno_v  varchar;
  chamado_r  bel_chamado%rowtype;
 idv      int8;
 a record;
 tempo_v  interval;
begin


 	-- return '#858796'; -- ''bg-secondary';
-- 	return '#ff0000'; -- ''bg-secondary';
-- 	return '#ffff00'; -- ''bg-secondary';

		   
	select 
 ts_abertura - now()
		  into tempo_v 
		  from bel_chamado
	where id_chamado = (
		select max( id_chamado ) 
		from bel_chamado 
		where id_terminal = id_terminal_p 
		and dt_atendimento is null );

if( not found ) then
	return '#858796'; -- ''bg-secondary';
end if;
	
	
if( tempo_v >= cast('00:02:00' as interval)) then
  return '#ff0000';
end if;

if( tempo_v >= cast('00:01:00' as interval)) then
  return '#ffff00';
end if;

if( tempo_v >= cast('00:00:00' as interval)) then
  return '#00ff00';
end if;

	return '#858796'||tempo_v; -- ''bg-secondary';
	
end;$function$


		
-- Function: public.bel_mensagem_bif()

-- DROP FUNCTION public.bel_mensagem_bif();

CREATE OR REPLACE FUNCTION public.bel_mensagem_bif()
  RETURNS trigger AS
$BODY$
declare
  retorno int8      := 0;
  linha_v int8      := 0;
  atualizado_v int8 := 0;
  a    record; -- cursor
  terminal_r    bel_terminal%rowtype;
  chamado_r     bel_chamado%rowtype;
begin
new.cliente_id := bel_cliente_add_f( new.cliente );
new.unidade_id := bel_unidade_add_f( new.unidade, new.cliente_id );
new.estacao_id := bel_estacao_add_f( new.estacao, new.cliente_id );
new.terminal_id := bel_terminal_add_f( new.terminal_name, new.cliente_id, new.terminal_tipo::integer );
  
  -- Estacao não permite incluir novos terminais
  --
--  if( bel_estacao_regra01f( new.cliente_id, new.estacao_id ) = 0 ) then
  --  return new;
  -- end if;
  
	
	update bel_terminal set
	  ult_botao  = new.terminal_botao 
	  , ult_ts = current_timestamp
	where id_terminal = new.terminal_id;

  select  atualizar_botao_f() into retorno;	

---------------------------------------------------------------------------
--                        CHAMADO                                      ----
---------------------------------------------------------------------------


-- abrir
select * into terminal_r
from bel_terminal
where id_terminal = new.terminal_id;



-- abrir chamado
----------------------------------------------------------------------

if( terminal_r.botao_ativar = new.terminal_botao ) then

  select * into chamado_r
  from bel_chamado
  where id_terminal = new.terminal_id
    and dt_atendimento is null;

  if( not found ) then  

    INSERT INTO bel_chamado(
      id_chamado
      , id_cliente, id_terminal
      , ts_abertura
    , dt_abertura
    ,  terminal_motivo)
  VALUES (
      nextval('bel_chamado_seq')
      , new.cliente_id, new.terminal_id
      , new.terminal_ts 
      , cast( new.terminal_ts as date )
      , new.terminal_motivo );
  end if;
end if;

-- cancelar / fechar chamado
----------------------------------------------------------------------
if( terminal_r.botao_desativar = new.terminal_botao ) then
    update bel_chamado set
      ts_atendimento = new.terminal_ts
      , dt_atendimento = cast( new.terminal_ts as date )
    where id_terminal = new.terminal_id
      and id_cliente = new.cliente_id
      and dt_atendimento is null;
end if;




---------------------------------------------------------------------------
--                        MONITORAMENTO                                ----
---------------------------------------------------------------------------


  
 
   -- Atualizar Monitoramento
   
	
		update bel_monitoramento set
		  ult_botao = new.terminal_botao
		  , ult_botao_ts = new.terminal_ts
		  , ult_botao_dt = cast( new.terminal_ts as date )
		where cliente_id = new.cliente_id  
		  and unidade_id = new.unidade_id
		  and estacao_id = new.estacao_id
		  and terminal_id = new.terminal_id;
		 
	  if( not found ) then
	    insert into bel_monitoramento(
	      cliente_id, unidade_id, estacao_id
	      , terminal_id, ult_botao
	      , ult_botao_ts, ult_botao_dt )
	    values(
	      new.cliente_id, new.unidade_id
	      , new.estacao_id
	      , new.terminal_id
	      , new.terminal_botao, new.inclusao_ts
	      , cast( new.inclusao_ts as date ) 
	      )  ;
	  end if;
 
	
	return new;
end;$BODY$
  LANGUAGE plpgsql ;
  
  
  
  
  
  
  


select id_mensagem, terminal_id, cliente_id, terminal_botao
, t.botao_ativar
, t.botao_desativar
,( select min( d.id_chamado ) from bel_chamado d 
     where d.id_terminal = m.terminal_id 
     and d.dt_atendimento is null ) as chamado_aberto
from
  bel_terminal t, 
  bel_mensagem m
where ( t.id_terminal = m.terminal_id )
order by id_mensagem desc
limit 10
/*
update bel_terminal set
  botao_ativar = '8'
  , botao_desativar = '4'
where id_tipo = 1 */






