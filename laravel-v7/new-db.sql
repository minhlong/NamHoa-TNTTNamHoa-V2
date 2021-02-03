create table permissions
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(255) not null,
    guard_name varchar(255) not null,
    note       varchar(500) null,
    created_at timestamp    null,
    updated_at timestamp    null
)
    collate = utf8mb4_unicode_ci;

create table roles
(
    id         bigint unsigned auto_increment
        primary key,
    name       varchar(255) not null,
    guard_name varchar(255) not null,
    note       varchar(500) null,
    created_at timestamp    null,
    updated_at timestamp    null
)
    collate = utf8mb4_unicode_ci;

create table taikhoan_has_permissions
(
    permission_id bigint unsigned not null,
    model_type    varchar(255)    not null,
    taikhoan_id   char(5)         not null,
    primary key (permission_id, taikhoan_id, model_type),
    constraint model_has_permissions_permission_id_foreign
        foreign key (permission_id) references permissions (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index model_has_permissions_model_id_model_type_index
    on taikhoan_has_permissions (taikhoan_id, model_type);

create table taikhoan_has_roles
(
    role_id     bigint unsigned not null,
    model_type  varchar(255)    not null,
    taikhoan_id char(5)         not null,
    primary key (role_id, taikhoan_id, model_type),
    constraint model_has_roles_role_id_foreign
        foreign key (role_id) references roles (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index model_has_roles_model_id_model_type_index
    on taikhoan_has_roles (taikhoan_id, model_type);

create table role_has_permissions
(
    permission_id bigint unsigned not null,
    role_id       bigint unsigned not null,
    primary key (permission_id, role_id),
    constraint role_has_permissions_permission_id_foreign
        foreign key (permission_id) references permissions (id)
            on delete cascade,
    constraint role_has_permissions_role_id_foreign
        foreign key (role_id) references roles (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;
