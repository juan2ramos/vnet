
SELECT * FROM `vocationet-dev`.`usuarios`;

SELECT * FROM `vocationet-dev`.`relaciones`;

-- 6 id de estudiante

SELECT u.id, u.usuario_nombre FROM 
usuarios u
JOIN relaciones r ON r.usuario_id = u.id OR r.usuario2_id = u.id
WHERE 
	(r.usuario_id = 6 OR r.usuario2_id = 6)
	AND u.id != 6
	AND r.tipo = 2
	AND r.estado = 1
;


SELECT * FROM `vocationet-dev`.`mentorias`;


SELECT u.id, u.usuario_nombre, m.* FROM 
usuarios u
JOIN relaciones r ON r.usuario_id = u.id OR r.usuario2_id = u.id
JOIN mentorias m ON u.id = m.usuario_mentor_id
WHERE 
	(r.usuario_id = 6 OR r.usuario2_id = 6)
	AND u.id != 6
	AND r.tipo = 2
	AND r.estado = 1
	AND (m.usuario_estudiante_id = 6 OR m.usuario_estudiante_id IS NULL)
;