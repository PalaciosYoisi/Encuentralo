ENCUÉNTRALO — ACTUALIZACIÓN IA Y CONTROL DEL AUTOR

Si YA importaste encuentralo_db anteriormente:
1. Abre http://localhost/phpmyadmin
2. Selecciona encuentralo_db.
3. Ve a Importar.
4. Importa SOLO actualizar_encuentralo_v2.sql.
5. Reemplaza tu carpeta del sitio por esta versión (conserva la carpeta uploads si ya tienes fotos que quieras mantener).
6. Abre el sitio y usa Ctrl+F5.

Si instalas desde cero, importa encuentralo_db.sql; ya incluye la columna nueva.

NOVEDADES:
- IA guarda una huella visual (embedding) de cada nueva fotografía.
- Las coincidencias muestran la publicación candidata, su imagen, porcentaje total y similitud visual.
- El porcentaje es una sugerencia; nunca confirma identidad por sí solo.
- Solo el autor puede marcar su reporte como recuperado/entregado, reactivarlo o eliminarlo.
- El tablero usa “Casos resueltos” en lugar de “Devueltos”.
- Se eliminó el emoji de saludo.

IMPORTANTE:
Los reportes antiguos no tienen huella visual porque se crearon antes de esta actualización. Seguirán comparándose por categoría, color, lugar y fecha. Los nuevos reportes con foto sí usarán comparación visual por IA.
