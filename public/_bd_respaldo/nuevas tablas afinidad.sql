create table ev_afinidad(
  id_afinidad       int not null auto_increment,
    id_empresa        int not null,
    des_afinidad      varchar(30),
    id_usuario_registra   int,
    st_vigente        char(1) default 'S',
    created_at        datetime default current_timestamp,
    updated_at        datetime default current_timestamp,
    foreign key (id_empresa) references ma_empresa(id_empresa),
    primary key (id_afinidad, id_empresa)
);
create table ev_afinidad_oficina(
  id_afinidad       int not null,
    id_empresa        int not null,
    id_oficina        int not null,
    id_usuario_registra   int,
    st_vigente        char(1) default 'S',
    created_at        datetime default current_timestamp,
    updated_at        datetime default current_timestamp,
    foreign key (id_afinidad, id_empresa) references ev_afinidad (id_afinidad, id_empresa),
    foreign key (id_oficina, id_empresa) references ma_oficina (id_oficina, id_empresa),
    primary key (id_afinidad, id_empresa, id_oficina)
);
create table ev_evaluacion(
    id_encuesta     int,
    id_empresa      int,
    id_usuario      int,
    st_evaluacion   varchar(15) default 'Pendiente',
    nu_progreso     int default 0,
    created_at      datetime default current_timestamp,
    updated_at      datetime default current_timestamp,
    foreign key (id_encuesta,id_empresa) references ma_encuesta(id_encuesta,id_empresa),
    foreign key (id_usuario,id_empresa) references us_usuario(id_usuario,id_empresa),
    primary key(id_encuesta,id_empresa,id_usuario)
);
ALTER TABLE `hls`.`ma_encuesta` 
ADD COLUMN `id_registra` INT NOT NULL AFTER `updated_at`;