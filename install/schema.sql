-- ═══════════════════════════════════════════════════════════════
-- PALADIN PROFILE v2 — MariaDB Schema
-- LUMINOUS Engine · pally-profile.goldhatconsulting.com
-- PHP 8.4 / MariaDB on IONOS Shared Hosting
-- ═══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `pally_profile` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pally_profile`;

-- ─── GAME DATA TABLES (admin-expandable) ───

CREATE TABLE `orders` (
  `id` VARCHAR(32) NOT NULL PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `spectrum` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `color_hex` VARCHAR(7) DEFAULT '#c8961a',
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `callings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` VARCHAR(32) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_custom` TINYINT(1) DEFAULT 0,
  `created_by_character` VARCHAR(36) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB;

CREATE TABLE `alignments` (
  `id` VARCHAR(4) NOT NULL PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `subtitle` VARCHAR(200) NOT NULL,
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE `attributes` (
  `id` VARCHAR(32) NOT NULL PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_core` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `specializations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `is_custom` TINYINT(1) DEFAULT 0,
  `created_by_character` VARCHAR(36) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_category` (`category`)
) ENGINE=InnoDB;

CREATE TABLE `talent_trees` (
  `id` VARCHAR(32) NOT NULL PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(10) NOT NULL,
  `description` TEXT,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE `talent_nodes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tree_id` VARCHAR(32) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `tier` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0-14, sequential unlock',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`tree_id`) REFERENCES `talent_trees`(`id`) ON DELETE CASCADE,
  INDEX `idx_tree_tier` (`tree_id`, `tier`)
) ENGINE=InnoDB;

CREATE TABLE `feats` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `level_req` INT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  INDEX `idx_level` (`level_req`)
) ENGINE=InnoDB;

-- ─── CHARACTER TABLES ───

CREATE TABLE `characters` (
  `id` VARCHAR(36) NOT NULL PRIMARY KEY COMMENT 'UUID v4',
  `share_slug` VARCHAR(16) UNIQUE COMMENT 'short public URL slug',
  `name` VARCHAR(100) NOT NULL,
  `title` VARCHAR(150) DEFAULT NULL,
  `oath_source` VARCHAR(100) DEFAULT NULL,
  `oath_name` VARCHAR(150) DEFAULT NULL,
  `oath_statement` TEXT,
  `order_id` VARCHAR(32) DEFAULT NULL,
  `alignment_id` VARCHAR(4) DEFAULT NULL,
  `calling_id` INT UNSIGNED DEFAULT NULL,
  `calling_custom` VARCHAR(100) DEFAULT NULL COMMENT 'if user typed custom calling',
  `specialization_id` INT UNSIGNED DEFAULT NULL,
  `specialization_custom` VARCHAR(100) DEFAULT NULL,
  `current_level` INT UNSIGNED DEFAULT 1,
  `is_public` TINYINT(1) DEFAULT 0,
  `edit_token` VARCHAR(64) NOT NULL COMMENT 'hashed bearer token for editing',
  `build_json` JSON COMMENT 'full attribute/talent/feat snapshot',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`alignment_id`) REFERENCES `alignments`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`calling_id`) REFERENCES `callings`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`specialization_id`) REFERENCES `specializations`(`id`) ON DELETE SET NULL,
  INDEX `idx_public` (`is_public`, `updated_at`),
  INDEX `idx_slug` (`share_slug`)
) ENGINE=InnoDB;

CREATE TABLE `character_attributes` (
  `character_id` VARCHAR(36) NOT NULL,
  `attribute_id` VARCHAR(32) NOT NULL,
  `base_value` INT UNSIGNED DEFAULT 8,
  `current_value` INT UNSIGNED DEFAULT 8,
  `justification` TEXT COMMENT 'Elder Scrolls style: why this increased',
  PRIMARY KEY (`character_id`, `attribute_id`),
  FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`attribute_id`) REFERENCES `attributes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `character_talents` (
  `character_id` VARCHAR(36) NOT NULL,
  `tree_id` VARCHAR(32) NOT NULL,
  `points_allocated` INT UNSIGNED DEFAULT 0,
  PRIMARY KEY (`character_id`, `tree_id`),
  FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tree_id`) REFERENCES `talent_trees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `character_feats` (
  `character_id` VARCHAR(36) NOT NULL,
  `feat_id` INT UNSIGNED NOT NULL,
  `unlocked_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `justification` TEXT COMMENT 'what real-life growth earned this feat',
  PRIMARY KEY (`character_id`, `feat_id`),
  FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`feat_id`) REFERENCES `feats`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── LEVEL-UP JOURNAL (the dream tracker) ───

CREATE TABLE `level_journal` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `character_id` VARCHAR(36) NOT NULL,
  `from_level` INT UNSIGNED NOT NULL,
  `to_level` INT UNSIGNED NOT NULL,
  `journal_entry` TEXT NOT NULL COMMENT 'what real growth justified this level-up',
  `mentor_name` VARCHAR(100) DEFAULT NULL,
  `mentor_type` VARCHAR(50) DEFAULT NULL COMMENT 'self, peer, teacher, spiritual, professional',
  `evidence_notes` TEXT COMMENT 'optional: links, certificates, milestones',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`character_id`) REFERENCES `characters`(`id`) ON DELETE CASCADE,
  INDEX `idx_char_level` (`character_id`, `to_level`)
) ENGINE=InnoDB;

-- ─── COMMUNITY SUGGESTIONS ───

CREATE TABLE `suggestions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `suggestion_type` ENUM('attribute','calling','specialization','talent','order','feat','other') NOT NULL,
  `suggested_name` VARCHAR(150) NOT NULL,
  `suggested_category` VARCHAR(100) DEFAULT NULL,
  `description` TEXT,
  `submitter_name` VARCHAR(100) DEFAULT NULL,
  `submitter_email` VARCHAR(200) DEFAULT NULL,
  `submitter_character_id` VARCHAR(36) DEFAULT NULL,
  `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`),
  INDEX `idx_type` (`suggestion_type`)
) ENGINE=InnoDB;

-- ═══════════════════════════════════════════════════════════════
-- SEED DATA
-- ═══════════════════════════════════════════════════════════════

-- Orders
INSERT INTO `orders` (`id`,`name`,`title`,`spectrum`,`description`,`color_hex`,`sort_order`) VALUES
('white','Order of the White','The Radiant Covenant','Pure Spiritual','Devoted entirely to divine connection. Healers, prophets, spiritual counselors. Their power flows from unwavering faith and communion with forces beyond mortal ken.','#f0ead6',1),
('black','Order of the Black','The Obsidian Accord','Shadow Conviction','Paladins who walk in darkness so others don\'t have to. Intelligence operatives, strategic thinkers who understand that sometimes the shield must become the sword.','#1a1a2e',2),
('brown','Order of the Brown','The Earthen Compact','Steward of Land','Connected to land, livestock, and legacy. Farmers, ranchers, environmental stewards. Their oath is to the earth itself and the communities rooted in it.','#6b4226',3),
('purple','Order of the Purple','The Arcane Throne','Mystical Authority','Where divine power meets arcane study. Scholars, researchers, those who believe knowledge itself is sacred. Battlemages and sage-knights.','#5b2c8e',4),
('blue','Order of the Blue','The Azure Shield','Law & Service','Guardians of civil order, justice, and institutional integrity. Law enforcement, military, emergency services. Their oath is to the structures that protect civilization.','#1a5276',5),
('green','Order of the Green','The Verdant Path','Growth & Renewal','Healers of systems — biological, ecological, economic. They see growth as sacred and stagnation as corruption. Entrepreneurs, therapists, teachers.','#1a6b3c',6),
('yellow','Order of the Yellow','The Solar Decree','Knowledge & Innovation','Engineers, inventors, architects of the future. Their oath is to illuminate ignorance with understanding. Technology in service of humanity.','#c9a922',7),
('orange','Order of the Orange','The Ember Pact','Courage & Action','First responders, activists, those who run toward danger. Their oath demands action over deliberation. Courage is the supreme virtue.','#c45e1a',8),
('red','Order of the Red','The Crimson Mandate','Pure Martial','Warriors first. Their oath is sealed in discipline, training, and the willingness to stand between harm and the innocent. Pure martial excellence.','#a81c1c',9),
('pink','Order of the Pink','The Rose Communion','Compassion & Art','Artists, performers, counselors, diplomats. Their oath is to beauty, empathy, and the healing power of creative expression.','#c44d8e',10);

-- Alignments
INSERT INTO `alignments` (`id`,`name`,`subtitle`,`sort_order`) VALUES
('LG','Lawful Good','The Paragon — Rules serve righteousness',1),
('NG','Neutral Good','The Benefactor — Results over methods',2),
('CG','Chaotic Good','The Rebel — Freedom serves justice',3),
('LN','Lawful Neutral','The Judge — Order above all',4),
('TN','True Neutral','The Balanced — Equilibrium is sacred',5),
('CN','Chaotic Neutral','The Maverick — Freedom above all',6),
('LE','Lawful Evil','The Tyrant — Order through dominion',7),
('NE','Neutral Evil','The Pragmatist — Self-interest refined',8),
('CE','Chaotic Evil','The Destroyer — Tear down to rebuild',9);

-- Core Attributes
INSERT INTO `attributes` (`id`,`name`,`description`,`sort_order`,`is_core`) VALUES
('str','Strength','Physical power, carry capacity, melee impact',1,1),
('end','Endurance','Stamina, health pool, resistance to fatigue',2,1),
('agi','Agility','Speed, reflexes, evasion, ranged accuracy',3,1),
('int','Intelligence','Knowledge, analysis, spell power, research',4,1),
('wil','Willpower','Mental fortitude, faith power, spell resistance',5,1),
('per','Personality','Charisma, leadership, persuasion, presence',6,1),
('wis','Wisdom','Perception, intuition, discernment, awareness',7,1),
('lck','Luck','Fortune, critical chance, divine favor, timing',8,1);

-- Callings (8 per order = 80 base)
INSERT INTO `callings` (`order_id`,`name`,`description`) VALUES
('white','Healer','Sacred restoration and mending of body, mind, and spirit'),
('white','Prophet','Seer of truth, bearer of divine messages and warnings'),
('white','Chaplain','Spiritual guide and counselor for communities and organizations'),
('white','Sanctifier','Consecrates spaces, objects, and relationships with sacred purpose'),
('white','Lightbearer','Walking beacon of hope, dispels despair through presence alone'),
('white','Oracle','Interpreter of signs, patterns, and divine will'),
('white','Peacemaker','Resolves conflict through compassion and sacred diplomacy'),
('white','Confessor','Keeper of secrets, healer of guilt, guardian of the confessional seal'),
('black','Inquisitor','Seeker of hidden truths, relentless investigator of corruption'),
('black','Shadow Operative','Works unseen to protect others from threats they never know about'),
('black','Justicar','Enforcer of oaths and consequences, the law behind the law'),
('black','Oathbreaker Redeemed','One who fell and rose again, stronger for the breaking'),
('black','Hex Knight','Studies the dark to defend against it, fights fire with understanding'),
('black','Raven Sentinel','Watches from the margins, reports to those who need to know'),
('black','Night Warden','Guards the vulnerable during their most exposed hours'),
('black','Void Scholar','Studies absence, entropy, and endings to understand beginnings'),
('brown','Warden','Protector of specific land, community, or natural resource'),
('brown','Ranger','Patrols boundaries, tracks threats, knows the territory intimately'),
('brown','Steward','Manages resources, ensures sustainability, plans for generations'),
('brown','Druid Knight','Merges martial discipline with deep ecological awareness'),
('brown','Earthcaller','Draws strength and wisdom from the land itself'),
('brown','Beast Guardian','Protector and advocate for animals and livestock welfare'),
('brown','Harvest Sentinel','Ensures food security, agricultural resilience, seasonal readiness'),
('brown','Rootwalker','Genealogist and heritage keeper, traces bloodlines and land rights'),
('purple','Arcane Knight','Combines scholarly pursuit with martial readiness'),
('purple','Sage','Pure knowledge seeker, walking library, advisor to leaders'),
('purple','Runebinder','Encodes knowledge into systems, patterns, and frameworks'),
('purple','Mystic Scholar','Studies the intersection of science and the unexplainable'),
('purple','Spellsword','Balanced practitioner of both intellectual and physical discipline'),
('purple','Lorekeeper','Archives history, preserves cultural memory, fights revisionism'),
('purple','Reality Analyst','Studies systems as they actually are, not as they claim to be'),
('purple','Chrono Warden','Studies patterns across time, predicts consequences of actions'),
('blue','Shield Bearer','The literal last line of defense, specializes in protection'),
('blue','Tactical Commander','Leads teams in structured operations under pressure'),
('blue','Field Marshal','Strategic oversight of large-scale coordinated efforts'),
('blue','Sentinel','Standing watch, maintaining vigilance, first to detect threats'),
('blue','Enforcer','Ensures compliance with agreed-upon rules and standards'),
('blue','Peacekeeper','Maintains order through presence, de-escalation, and authority'),
('blue','Crisis Responder','First on scene, trained for emergencies, calm under chaos'),
('blue','Bulwark','Immovable defender of institutions, processes, and infrastructure'),
('green','Cultivator','Grows people, organizations, and ecosystems to their potential'),
('green','Mentor','Dedicated to developing others through teaching and example'),
('green','Restorer','Repairs what is broken — relationships, systems, communities'),
('green','Life Warden','Protects the conditions necessary for growth and healing'),
('green','Growth Strategist','Plans expansion, identifies opportunity, removes obstacles'),
('green','Renewal Knight','Specializes in transformation after crisis or stagnation'),
('green','Seed Bearer','Plants ideas, initiatives, and organizations for future harvest'),
('green','Sanctuary Builder','Creates safe spaces for healing, learning, and development'),
('yellow','Artificer','Builds tools, systems, and technologies that serve the oath'),
('yellow','Systems Architect','Designs complex interconnected systems for reliability'),
('yellow','Innovation Knight','Pushes boundaries of what technology can accomplish for good'),
('yellow','Solar Engineer','Specializes in energy, sustainability, and powering the future'),
('yellow','Beacon','Makes complex knowledge accessible and actionable for everyone'),
('yellow','Forge Master','Creates lasting works — physical or digital — of enduring quality'),
('yellow','Signal Knight','Communications specialist, ensures truth reaches those who need it'),
('yellow','Circuit Warden','Protects digital infrastructure, cybersecurity, and data integrity'),
('orange','Vanguard','Always first in, leads from the front, inspires through action'),
('orange','Flame Knight','Passionate advocate who ignites movements and mobilizes people'),
('orange','Battle Medic','Heals under fire, maintains composure when others cannot'),
('orange','Storm Breaker','Confronts overwhelming opposition head-on and creates openings'),
('orange','Rally Commander','Turns fear into courage, disorganization into coordinated action'),
('orange','Ember Warden','Keeps hope alive in prolonged difficulty, prevents burnout'),
('orange','Charge Captain','Specializes in decisive action when deliberation has failed'),
('orange','Frontline','The face of any cause, absorbs attention so others can work'),
('red','Weapon Master','Peak proficiency with chosen discipline, martial perfection'),
('red','Gladiator','Thrives in direct confrontation, competitive excellence'),
('red','War Paladin','Full integration of martial skill with oath-driven purpose'),
('red','Iron Champion','Endurance specialist, outlasts any opposition through pure will'),
('red','Crimson Knight','Traditional warrior-oath, bound by honor codes and combat rites'),
('red','Blade Oath','Sword and oath are one — the weapon IS the sacred instrument'),
('red','Steel Guardian','Defensive martial master, protects through superior combat position'),
('red','Combat Arbiter','Settles disputes through structured, fair physical contest'),
('pink','Muse Knight','Inspires creative work in others, patron of artists and makers'),
('pink','Diplomat','Masters the art of negotiation, finds win-win in every conflict'),
('pink','Hearth Guardian','Protects the home, family unit, and domestic sanctuary'),
('pink','Song Warden','Uses music, voice, and performance as instruments of the oath'),
('pink','Artisan Paladin','Creates beauty with their hands — craftsperson as holy act'),
('pink','Rose Templar','Combines compassion with structured discipline, gentle strength'),
('pink','Compassion Knight','Empathy as a weapon against cruelty, softness as strength'),
('pink','Dream Weaver','Helps others envision and pursue their highest potential');

-- Specializations
INSERT INTO `specializations` (`category`,`name`,`description`) VALUES
('STEM','Cybersecurity Specialist','Defensive and offensive security operations, penetration testing, threat analysis'),
('STEM','AI/ML Engineer','Machine learning systems, neural networks, data pipelines, ethical AI governance'),
('STEM','Systems Architect','Enterprise infrastructure, cloud architecture, distributed systems, DevOps'),
('STEM','Biomedical Researcher','Medical research, genomics, pharmacology, clinical trials'),
('STEM','Environmental Scientist','Ecology, climate systems, conservation biology, sustainability'),
('STEM','Quantum Computing Researcher','Quantum algorithms, error correction, quantum-classical hybrid systems'),
('STEM','Software Engineer','Full-stack development, API design, database architecture, testing'),
('STEM','Data Scientist','Statistical modeling, predictive analytics, data visualization'),
('HUMANITIES','Clinical Psychologist','Trauma therapy, cognitive behavioral methods, intergenerational healing'),
('HUMANITIES','Historian / Archivist','Primary source research, genealogy, cultural preservation'),
('HUMANITIES','Theologian','Comparative religion, scriptural analysis, interfaith dialogue'),
('HUMANITIES','Philosopher / Ethicist','Applied ethics, moral philosophy, institutional governance'),
('HUMANITIES','Linguist / Translator','Computational linguistics, cross-cultural communication'),
('HUMANITIES','Sociologist','Social systems analysis, community dynamics, institutional behavior'),
('BUSINESS','Strategic Consultant','Organizational transformation, market analysis, operational excellence'),
('BUSINESS','Non-Profit Director','Mission-driven leadership, grant writing, community impact'),
('BUSINESS','Financial Analyst','Portfolio management, risk assessment, economic forecasting'),
('BUSINESS','Supply Chain Strategist','Global logistics, procurement, lean operations'),
('BUSINESS','Entrepreneur','Venture creation, product-market fit, scaling organizations'),
('LAW & POLICY','Constitutional Law Scholar','Civil liberties, precedent analysis, policy advocacy'),
('LAW & POLICY','International Relations Specialist','Diplomacy, conflict resolution, geopolitics'),
('LAW & POLICY','Criminal Justice Reformer','Restorative justice, policy reform, community safety'),
('LAW & POLICY','Legislative Analyst','Policy drafting, regulatory impact, government affairs'),
('MEDICINE','Trauma Surgeon','Emergency medicine, battlefield triage, surgical intervention'),
('MEDICINE','Psychiatrist','Psychopharmacology, mental health treatment, crisis intervention'),
('MEDICINE','Public Health Director','Epidemiology, health systems, community wellness programs'),
('MEDICINE','Paramedic / EMT','Pre-hospital emergency care, rapid assessment, field medicine'),
('CREATIVE','Game Designer','Systems design, narrative mechanics, player experience architecture'),
('CREATIVE','Documentary Filmmaker','Investigative storytelling, visual narrative, social impact media'),
('CREATIVE','Author / Narrative Designer','Long-form prose, worldbuilding, interactive fiction'),
('CREATIVE','Music Director / Composer','Orchestral arrangement, worship music, sonic storytelling'),
('CREATIVE','Visual Artist','Painting, sculpture, digital art, gallery curation'),
('CREATIVE','Theater Director','Stage production, ensemble leadership, dramatic interpretation'),
('TRADES','Master Electrician','Power systems, renewable energy installation, code compliance'),
('TRADES','Master Cattleman','Livestock management, veterinary basics, agricultural stewardship'),
('TRADES','Forge Smith','Metalworking, bladesmithing, traditional and modern fabrication'),
('TRADES','Master Carpenter','Structural woodworking, furniture making, architectural restoration'),
('TRADES','Mechanic / Machinist','Engine systems, CNC operation, precision manufacturing'),
('MILITARY','Special Operations Planner','Mission planning, asymmetric warfare, personnel leadership'),
('MILITARY','Intelligence Analyst','SIGINT/HUMINT synthesis, threat assessment, counter-intelligence'),
('MILITARY','Combat Medic','Tactical medicine, field surgery, rescue operations under fire'),
('MILITARY','Logistics Officer','Supply chain warfare, resource allocation under constraints'),
('EDUCATION','Professor / Lecturer','Higher education instruction, curriculum development, research mentorship'),
('EDUCATION','K-12 Teacher','Classroom instruction, student development, educational innovation'),
('EDUCATION','Corporate Trainer','Professional development, skills transfer, organizational learning'),
('MINISTRY','Pastor / Minister','Congregational leadership, spiritual counseling, sermon preparation'),
('MINISTRY','Missionary','Cross-cultural outreach, community development, faith-based aid'),
('MINISTRY','Youth Minister','Adolescent mentorship, program development, family support');

-- Talent Trees
INSERT INTO `talent_trees` (`id`,`name`,`icon`,`description`,`sort_order`) VALUES
('arcane','Arcane / Supernatural','✦','Super-specialized skills that appear as magic to most people. Divine sensing, laying on hands, sacred channeling, aura projection.',1),
('martial','Martial / Physical','⚔','Combat, athletics, and physical mastery. Shield work, weapon proficiency, tactical awareness, battle stamina.',2),
('professional','Professional / Doctorate','🎓','Specialization into modern industry doctorate-level expertise. Research methods, publication, expert testimony.',3),
('social','Social / Creative / Performative','🎭','Soft skills, creative expression, performance. Public speaking, negotiation, storytelling, cultural fluency.',4);

-- Talent Nodes
INSERT INTO `talent_nodes` (`tree_id`,`name`,`description`,`tier`) VALUES
('arcane','Divine Sense','Detect the presence of strong conviction, deception, or spiritual energy nearby',0),
('arcane','Lay on Hands','Provide meaningful comfort and restoration through presence and touch',1),
('arcane','Sacred Flame','Channel intense focus and conviction that others can physically feel',2),
('arcane','Aura of Protection','Your calm presence reduces anxiety and fear in those around you',3),
('arcane','Zone of Truth','Create spaces where honesty becomes the natural default',4),
('arcane','Banishing Strike','Decisively remove negative influences from a situation or space',5),
('arcane','Holy Weapon','Imbue your primary tool or skill with sacred purpose',6),
('arcane','Divine Smite','Deliver truth with such force that denial becomes impossible',7),
('arcane','Radiant Shield','Your reputation precedes you, deflecting casual attacks',8),
('arcane','Oath Aura','Your oath is so internalized it manifests as tangible atmosphere',9),
('arcane','Spirit Guardians','Those who share your oath rally instinctively to your call',10),
('arcane','Consecrate Ground','Transform any space into a place of purpose and safety',11),
('arcane','Ward of Dawn','Protect an entire community or organization through sustained effort',12),
('arcane','Celestial Fury','Channel absolute conviction into a single decisive action',13),
('arcane','Miracle','Achieve something others considered impossible through pure faith and work',14),
('martial','Shield Wall','Establish firm personal boundaries that others respect instinctively',0),
('martial','Weapon Mastery','Achieve proficiency with your primary tool of trade',1),
('martial','Battle Stance','Maintain composure and readiness under sustained pressure',2),
('martial','Tactical Awareness','Read rooms, situations, and power dynamics accurately',3),
('martial','Second Wind','Recover from setbacks faster than expected through trained resilience',4),
('martial','Iron Will','Resist manipulation, coercion, and pressure to compromise your oath',5),
('martial','Rally Cry','Motivate a team through personal example during difficulty',6),
('martial','Counter Strike','Turn an opponent\'s aggression into an opening for your cause',7),
('martial','Armor Expertise','Develop thick skin without losing sensitivity where it matters',8),
('martial','War Horn','Your call to action reaches further and inspires more deeply',9),
('martial','Adrenaline Rush','Perform at peak capacity during genuine crisis',10),
('martial','Unyielding','Continue functioning effectively when others would quit',11),
('martial','Weapon Bond','Your tools and skills become extensions of your identity',12),
('martial','Martial Perfection','Your discipline in your domain becomes an art form',13),
('martial','Legend Stance','Achieve a level of mastery that becomes referenced by others',14),
('professional','Research Method','Apply rigorous methodology to any problem domain',0),
('professional','Data Analysis','Extract meaningful patterns from complex information',1),
('professional','Peer Review','Submit your work to scrutiny and improve from criticism',2),
('professional','Grant Writing','Secure resources by articulating vision compellingly in writing',3),
('professional','Lab Proficiency','Master the tools and environments specific to your field',4),
('professional','Field Study','Conduct real-world observation and evidence gathering',5),
('professional','Publication','Produce work that contributes to your field\'s body of knowledge',6),
('professional','Teaching','Transfer expertise effectively to others at any level',7),
('professional','Consultation','Provide expert guidance to organizations and individuals',8),
('professional','Patent Filing','Protect and formalize novel ideas and innovations',9),
('professional','Expert Witness','Testify credibly on matters within your expertise',10),
('professional','Board Certification','Achieve the highest recognized credential in your domain',11),
('professional','Thesis Defense','Articulate and defend a complex original argument under scrutiny',12),
('professional','Industry Leadership','Shape the direction and standards of your professional field',13),
('professional','Keynote Authority','Recognized as a primary voice and reference in your domain',14),
('social','Public Speaking','Communicate effectively to groups of any size',0),
('social','Negotiation','Find mutually beneficial outcomes in competing interests',1),
('social','Empathic Reading','Accurately perceive the emotional state and needs of others',2),
('social','Storytelling','Convey complex ideas through narrative that resonates emotionally',3),
('social','Presence','Command attention and respect through bearing, not volume',4),
('social','Inspiration','Cause others to believe in possibilities they had dismissed',5),
('social','Mediation','Guide opposing parties toward resolution without taking sides',6),
('social','Cultural Fluency','Navigate diverse social contexts without causing offense',7),
('social','Performance Art','Express truth through creative medium — music, theater, visual art',8),
('social','Team Building','Assemble and develop groups that perform beyond individual sum',9),
('social','Crisis Communication','Deliver difficult messages with clarity and compassion',10),
('social','Mentorship','Guide another person\'s development over sustained time',11),
('social','Networking','Build and maintain relationships across diverse communities',12),
('social','Legacy Building','Create systems and traditions that outlive your direct involvement',13),
('social','Legendary Influence','Your words and example shape decisions you never directly touch',14);

-- Feats
INSERT INTO `feats` (`level_req`,`name`,`description`) VALUES
(5,'Oath Hardened','Your oath crystallizes. +2 to primary attribute. Unlock Order-specific passive ability.'),
(10,'Aura Expansion','Your presence extends. Allies within your sphere gain your aura benefits. First capstone.'),
(15,'Multi-Discipline','Cross-train into a second Calling. Gain 3 skills from another path.'),
(20,'Legendary Resilience','Second capstone. Immune to fear and self-doubt. Your oath cannot be externally broken.'),
(25,'Master Specialist','Your doctorate specialization reaches mastery. Recognized as an Expert in your field.'),
(30,'Commander Presence','Third capstone. Lead groups of up to 50. Your aura of influence doubles in range.'),
(35,'Dual Oath','Take a secondary oath. Gain access to a second Color Order\'s talent tree.'),
(40,'Mythic Fortitude','Fourth capstone. Once per crisis, ignore a catastrophic setback. The oath sustains you.'),
(45,'Archon\'s Call','Your reputation precedes you globally. Organizations seek your counsel and partnership.'),
(50,'Paragon Ascension','Final capstone. You become a legend of your Order. Unlock Paragon abilities. Write your legacy.');
