
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------
-- Table: admins
-- ---------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ---------------------------------------------------
-- Table: categories
-- ---------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Road & Bridge', 'road-bridge'),
(2, 'Building Supervision', 'building-supervision'),
(3, 'Water Infrastructure', 'water-infrastructure'),
(4, 'Environmental', 'environmental'),
(5, 'Company News', 'company-news');

-- ---------------------------------------------------
-- Table: tags
-- ---------------------------------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(60) NOT NULL,
  `slug` VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tags` (`id`, `name`, `slug`) VALUES
(1, 'Bridges', 'bridges'),
(2, 'EIA', 'eia'),
(3, 'RAP', 'rap'),
(4, 'Feasibility', 'feasibility'),
(5, 'Water Wells', 'water-wells'),
(6, 'Housing', 'housing'),
(7, 'Sole Contract', 'sole-contract'),
(8, 'ERA', 'era');

-- ---------------------------------------------------
-- Table: posts
-- ---------------------------------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `excerpt` VARCHAR(400) DEFAULT NULL,
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(500) DEFAULT NULL,
  `category_id` INT UNSIGNED DEFAULT NULL,
  `author_name` VARCHAR(120) DEFAULT 'Beles Engineering Team',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  INDEX `idx_status_published` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------
-- Table: post_tags (many-to-many)
-- ---------------------------------------------------
DROP TABLE IF EXISTS `post_tags`;
CREATE TABLE `post_tags` (
  `post_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`, `tag_id`),
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------
-- Seed posts (the 6 example posts from the mockup)
-- ---------------------------------------------------
INSERT INTO `posts`
(`id`,`title`,`slug`,`excerpt`,`content`,`featured_image`,`category_id`,`author_name`,`is_featured`,`status`,`published_at`)
VALUES
(1,
 'Inside Package I: How six bridges in SNNP Region went from feasibility to final design',
 'inside-package-i-six-bridges-snnp-region',
 'A look at the route selection, structural design, and resettlement planning process behind one of our most complex sole-contract assignments — and what it took to deliver six bridges without a single subcontractor.',
 '<p class="post-lead">When the Ethiopian Roads Administration commissioned Beles Consulting for the Package I bridge programme, the brief covered six structures across Southern Nations, Nationalities &amp; Peoples Region — Kefo, Sheshe-02, Janjecho, Selele, Ameka, and Mite Jame. Every phase, from route selection through to final tender documentation, was delivered entirely in-house.</p>
<p>Most bridge programmes of this scale involve multiple consultants working in parallel, each responsible for a different structure or discipline. Package I took a different approach: one team, one set of standards, and one point of accountability across all six sites.</p>
<h2>Starting with route selection and feasibility</h2>
<p>Before any structural design began, our team conducted route selection studies for each crossing — assessing hydrology, soil conditions, and the surrounding road network to determine the most viable alignment.</p>
<h2>Structural design across six distinct sites</h2>
<p>No two of the six bridges shared identical site conditions. Material and site investigation reports informed structural design choices specific to each location.</p>
<h3>What the final deliverable package included</h3>
<ul>
<li>Final Inception Report and Route Selection Study for each of the six bridges</li>
<li>Feasibility Study and Environmental Impact Assessment Report</li>
<li>Full Structural Design Report per structure</li>
<li>Resettlement Action Plan and Land Acquisition Report</li>
<li>Complete bidding documents, including A3 drawings and Engineer''s Estimate</li>
</ul>
<p>Package I was completed on a 12-month, sole-contract basis — a meaningful proof point for what an in-house multi-discipline team can deliver on a tight timeline.</p>',
 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?q=80&w=900&auto=format&fit=crop',
 1, 'Beles Engineering Team', 1, 'published', '2026-06-14 09:00:00'),

(2,
 'What goes into designing a deep well system for Afar''s climate',
 'designing-deep-well-system-afar-climate',
 'Lessons from the Asaita water infrastructure project, including how we approached earthquake-resilient design for remote well production.',
 '<p class="post-lead">The Asaita Water Infrastructure and Well Production Project, valued at over 78.5 million ETB, required detailed design work that accounted for the unique demands of Afar Region''s terrain and climate.</p>
<p>Earthquake resilience was a key design consideration throughout, alongside an Environmental and Social Impact Assessment that shaped the final water supply system layout.</p>
<h2>Construction supervision as investment protection</h2>
<p>Our supervision team verified that contractor deliverables matched contract specifications at every stage, protecting the client''s investment from design through to commissioning.</p>',
 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?q=80&w=700&auto=format&fit=crop',
 3, 'Beles Engineering Team', 0, 'published', '2026-06-02 09:00:00'),

(3,
 'Supervising 300+ condominium blocks: what quality control actually looks like on site',
 'supervising-300-condominium-blocks-quality-control',
 'A behind-the-scenes look at resident supervision practices on the Bole Arabsa and Nifas Silk Lafto housing programmes.',
 '<p class="post-lead">Across the Bole Arabsa and Nifas Silk Lafto Sub-City housing programmes, Beles Consulting supervised the construction of over 300 residential blocks — a mix of G+2, G+4, and G+7 structures.</p>
<h2>Daily reporting and material testing</h2>
<p>Resident supervision meant daily site reports, issuance of material test orders, and certification of monthly work progress — all feeding into a consistent quality assurance process across every block.</p>',
 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?q=80&w=700&auto=format&fit=crop',
 2, 'Beles Engineering Team', 0, 'published', '2026-05-20 09:00:00'),

(4,
 'Route selection 101: how we choose the alignment for a new road project',
 'route-selection-101-road-alignment',
 'An overview of the technical and environmental factors that shape our route selection studies before design even begins.',
 '<p class="post-lead">Before a single design drawing is produced, every road project at Beles Consulting begins with a route selection study — a process that balances engineering feasibility against environmental and social impact.</p>
<h2>What we evaluate</h2>
<ul>
<li>Topography and drainage patterns</li>
<li>Existing land use and right-of-way constraints</li>
<li>Soil and material availability near the corridor</li>
<li>Community and environmental sensitivity</li>
</ul>',
 'https://images.unsplash.com/photo-1515162305285-0293e4767cc2?q=80&w=700&auto=format&fit=crop',
 1, 'Beles Engineering Team', 0, 'published', '2026-05-08 09:00:00'),

(5,
 'Why every Beles project starts with an Environmental and Social Impact Assessment',
 'every-project-starts-with-esia',
 'How ESIA shapes our design decisions from day one, and why it''s never treated as an afterthought.',
 '<p class="post-lead">Environmental and Social Impact Assessment isn''t a formality we complete after design — it''s a process that runs in parallel with feasibility studies, shaping decisions before they''re finalized.</p>
<p>This approach has informed everything from bridge alignment in SNNP Region to well placement in Afar and Somali Regions.</p>',
 'https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=700&auto=format&fit=crop',
 4, 'Beles Engineering Team', 0, 'published', '2026-04-26 09:00:00'),

(6,
 'Beles Consulting marks 18 years of sole-contract infrastructure delivery',
 'beles-consulting-18-years-sole-contract',
 'Reflecting on nearly two decades of projects — from the Mizan Aman Airfield to our latest water infrastructure assignments.',
 '<p class="post-lead">Since October 2007, Beles Consulting P.L.C. has delivered feasibility studies, detailed engineering design, environmental assessment, building supervision, and water infrastructure consultancy — always on a sole-contract basis.</p>
<h2>Looking back, and ahead</h2>
<p>From the Mizan Aman Airfield to the Asaita and Wabishebele water infrastructure projects, our approach has stayed consistent: one accountable, in-house team for every assignment.</p>',
 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=700&auto=format&fit=crop',
 5, 'Beles Engineering Team', 0, 'published', '2026-04-11 09:00:00');

-- ---------------------------------------------------
-- Seed post_tags relationships
-- ---------------------------------------------------
INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(1,1),(1,2),(1,3),(1,4),(1,7),(1,8),
(2,5),(2,7),
(3,6),(3,7),
(4,4),(4,8),
(5,2),(5,4),
(6,7),(6,8);

SET FOREIGN_KEY_CHECKS = 1;
