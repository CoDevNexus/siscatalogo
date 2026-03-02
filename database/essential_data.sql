/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','Acceso completo al sistema',1,'2026-02-28 19:42:08'),(2,'Viewer','Solo lectura (sin modificar datos)',0,'2026-02-28 19:42:08'),(3,'usuario','Usuario comun',0,'2026-02-28 20:23:57');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard.ver','Ver dashboard','dashboard'),(2,'productos.ver','Ver inventario','productos'),(3,'productos.crear','Crear productos','productos'),(4,'productos.editar','Editar productos','productos'),(5,'productos.eliminar','Eliminar productos','productos'),(6,'pedidos.ver','Ver pedidos','pedidos'),(7,'pedidos.gestionar','Gestionar pedidos','pedidos'),(8,'categorias.ver','Ver categorias','categorias'),(9,'categorias.gestionar','Gestionar categorias','categorias'),(10,'portfolio.ver','Ver casos de exito','portfolio'),(11,'portfolio.gestionar','Gestionar casos de exito','portfolio'),(12,'digitales.ver','Ver entregas digitales','digitales'),(13,'digitales.gestionar','Gestionar entregas digitales','digitales'),(14,'usuarios.ver','Ver usuarios','usuarios'),(15,'usuarios.crear','Crear usuarios','usuarios'),(16,'usuarios.editar','Editar usuarios','usuarios'),(17,'usuarios.eliminar','Eliminar usuarios','usuarios'),(18,'roles.ver','Ver roles y permisos','roles'),(19,'roles.gestionar','Gestionar roles y permisos','roles'),(20,'bitacora.ver','Ver bitacora','bitacora'),(21,'configuracion.ver','Ver configuracion empresa','configuracion'),(22,'configuracion.editar','Editar configuracion empresa','configuracion'),(23,'slider.gestionar','Gestionar slider y home','configuracion'),(24,'categorias.crear','Crear categorias','categorias'),(25,'categorias.eliminar','Eliminar categorias','categorias'),(26,'digitales.crear','Crear entregas digitales','digitales'),(27,'digitales.eliminar','Eliminar entregas digitales','digitales'),(28,'pedidos.eliminar','Eliminar pedidos','pedidos'),(29,'portfolio.crear','Crear casos de exito','portfolio'),(30,'portfolio.eliminar','Eliminar casos de exito','portfolio'),(31,'roles.crear','Crear roles','roles'),(32,'roles.eliminar','Eliminar roles','roles'),(33,'bitacora.eliminar','Eliminar bitacora','bitacora');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (2,1),(3,1),(2,2),(3,2),(3,3),(3,4),(2,6),(3,6),(3,7),(2,8),(3,8),(3,9),(2,10),(3,10),(2,12),(3,12),(3,13),(2,18),(2,20),(3,20),(2,21),(3,21),(3,22),(3,23);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','$2y$10$16sR1iDOytQV/GPOdfoh1.DpDAZqkGmtbXFwCtoyYH2J4E.qVKBGa','admin',1,'jose29di@gmail.com','2026-02-27 23:32:23'),(2,'demo','$2y$10$wn/ex6TcrswFdmVUWDYhOOXCaiZ5KUAOfLtdwOCv/g1sOiTY.KNy2','viewer',3,'jmejiapluas@gmail.com','2026-02-28 20:18:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `company_profile` WRITE;
/*!40000 ALTER TABLE `company_profile` DISABLE KEYS */;
INSERT INTO `company_profile` VALUES (1,'Publimarket','09123456789001','Av. principal y calle secundaria','593990760095','jmejiapluas@gmail.com','https://i.ibb.co/YBHSpn0j/4ef4e38fe1c9.jpg','','','2026-03-01 02:54:58','593990760095','','','Somos una empresa que crea lo que tu te imaginas, un cliente feliz demuestra confía en nosotros','Tus imaginacion es nuestra inspiracion','Guayaquil','','Cotización valida por 48 horas laborabes','¡Gracias por su preferencia!','<iframe src=\"https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d15948.640091866526!2d-79.96537568051758!3d-2.0916401395792716!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1ses!2sec!4v1772333670445!5m2!1ses!2sec\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>',5.00,16.00,'smtp.gmail.com',587,'clientesgye@gmail.com','lyqg bdso kfyo xtcq','tls','jose29di@gmail.com','SisCatalogo','assets/img/footer_publicidad.jpg','8671509617:AAF_b0BkY8KAt68PItxD1HA0c1PFbtigKgU','1511692965',1,'#3c214b','#ef233c','#1332c4','#1332c4');
/*!40000 ALTER TABLE `company_profile` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `home_settings` WRITE;
/*!40000 ALTER TABLE `home_settings` DISABLE KEYS */;
INSERT INTO `home_settings` VALUES ('featured_desc','Nuestros artículos más populares y recientes.','Descripción de Productos Destacados','featured'),('featured_title','Productos Destacados','Título de Productos Destacados','featured'),('hero_badge','Nuevos productos 2026','Etiqueta (Badge)','hero'),('hero_desc','Personalizamos MDF, acrilico y ofrecemos los mejores vectores digitales listos para tus propias maquinas de corte y trabajos en DTF.','Descripci├│n','hero'),('hero_image','','Imagen de Portada (Cat├ílogo)','hero'),('hero_placeholder','Buscar llavero, caja, vector...','Texto Buscador','hero'),('hero_title','Eleva tu Marca con Pintmax','T├¡tulo Principal','hero'),('portfolio_desc','Descubre cómo ayudamos a otros a hacer realidad sus ideas.','Descripción de Sección Portafolio','portfolio'),('portfolio_title','Casos de Éxito','Título de Sección Portafolio','portfolio');
/*!40000 ALTER TABLE `home_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

