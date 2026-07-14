-- ============================================================
-- Migrimi i bazës së të dhënave `sewpro` në skemën e re.
-- Ekzekutoni një herë në phpMyAdmin ose me:
--   C:\xampp\mysql\bin\mysql.exe -u root sewpro < migration.sql
--
-- Çfarë rregullon ky migrim:
--  1. Fshin trigger-in `before_insert_self_grades` që rriste "muajin"
--     me çdo notë të re (burimi kryesor i gabimeve në llogaritje).
--     Tani muaji nxirret gjithmonë nga data reale e notës (grade_date).
--  2. Heq kolonat e panevojshme `month` dhe `year` nga self_grades.
--  3. Fshin dublikatat dhe shton çelës unik (nxënës, lëndë, datë) -
--     kështu "një vlerësim në ditë për lëndë" garantohet nga baza.
--  4. Shton gjurmim të aprovimit (kush e aprovoi dhe kur).
--  5. Fshin tabelat `final_grades` dhe `monthly_grades` - notat
--     përfundimtare llogariten tani në kohë reale nga notat ditore
--     (të dhënat ekzistuese aty ishin të pasakta, të gjitha 0.00).
--  6. Fshin `remember_tokens` - "Ruaj llogarinë" tani ruan vetëm
--     emrin e përdoruesit, jo fjalëkalimin.
--
-- Shënim për fjalëkalimet: hash-imi bëhet automatikisht në kyçjen
-- e parë të çdo përdoruesi (login.php i konverton fjalëkalimet e
-- vjetra në password_hash pa ndërhyrje manuale).
-- ============================================================

DROP TRIGGER IF EXISTS before_insert_self_grades;

-- Fshij dublikatat: mbaj rreshtin më të vjetër për (nxënës, lëndë, datë)
DELETE sg1 FROM self_grades sg1
JOIN self_grades sg2
  ON sg1.student_id = sg2.student_id
 AND sg1.subject_id = sg2.subject_id
 AND sg1.grade_date = sg2.grade_date
 AND sg1.grade_id > sg2.grade_id;

ALTER TABLE self_grades
  DROP COLUMN month,
  DROP COLUMN year,
  ADD COLUMN approved_by INT(11) NULL DEFAULT NULL,
  ADD COLUMN approved_at DATETIME NULL DEFAULT NULL,
  ADD UNIQUE KEY uniq_student_subject_date (student_id, subject_id, grade_date);

DROP TABLE IF EXISTS monthly_grades;
DROP TABLE IF EXISTS final_grades;
DROP TABLE IF EXISTS remember_tokens;

-- Zgjero fushën e fjalëkalimit për hash-et moderne
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;
