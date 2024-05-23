-- esquema Mysql

CREATE TABLE recetas(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nombre VARCHAR(255) NOT NULL,
	descripcion VARCHAR(2048) NOT NULL,
	porciones INT NOT NULL
);

CREATE TABLE ingredientes_recetas(
	id_receta BIGINT UNSIGNED NOT NULL,
	nombre VARCHAR(255) NOT NULL,
	cantidad INT NOT NULL,
	unidad_medida VARCHAR(255) NOT NULL,
	FOREIGN KEY (id_receta) REFERENCES recetas(id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE pasos_recetas(
	id_receta BIGINT UNSIGNED NOT NULL,
	paso VARCHAR(255) NOT NULL,
	FOREIGN KEY (id_receta) REFERENCES recetas(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE fotos_recetas(
	id_receta BIGINT UNSIGNED NOT NULL,
	foto VARCHAR(255) NOT NULL,
	FOREIGN KEY (id_receta) REFERENCES recetas(id) ON DELETE CASCADE ON UPDATE CASCADE
);



--esquema Postgres

CREATE TABLE recetas (
  id SERIAL PRIMARY KEY,  -- Use SERIAL for auto-incrementing ID
  nombre VARCHAR(255) NOT NULL,
  descripcion VARCHAR(2048) NOT NULL,
  porciones INT NOT NULL
);

CREATE TABLE ingredientes_recetas (
  id_receta BIGINT REFERENCES recetas(id) ON DELETE CASCADE,
  nombre VARCHAR(255) NOT NULL,
  cantidad INT NOT NULL,
  unidad_medida VARCHAR(255) NOT NULL
);

CREATE TABLE pasos_recetas (
  id_receta BIGINT REFERENCES recetas(id) ON DELETE CASCADE,
  paso VARCHAR(255) NOT NULL
);

CREATE TABLE fotos_recetas (
  id_receta BIGINT REFERENCES recetas(id) ON DELETE CASCADE,
  foto VARCHAR(255) NOT NULL
);
