<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3>Daftar Nilai Akademik</h3>
        <?php
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT related_id FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $u = $stmt->fetch();
        $student_id = $u['related_id'];

        // Find active enrollment
        $stmt = $pdo->prepare("SELECT c.name as class_name, c.id as class_id, ay.name as year_name, se.id as enrollment_id 
                               FROM student_enrollments se 
                               JOIN classes c ON se.class_id = c.id 
                               JOIN academic_years ay ON se.academic_year_id = ay.id
                               WHERE se.student_id = ? AND se.status = 'Active' 
                               ORDER BY se.id DESC LIMIT 1");
        $stmt->execute([$student_id]);
        $enrollment = $stmt->fetch();
        ?>
        <?php if ($enrollment): ?>
        <div class="badge" style="background: #EFF6FF; color: #2563EB; font-size: 0.9rem;">
            Semester Ganjil - <?php echo htmlspecialchars($enrollment['year_name']); ?>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($enrollment): ?>
        <div style="overflow-x: auto;">
             <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                    <tr>
                        <th style="text-align: left; padding: 12px; font-weight: 600; color: #475569;">Mata Pelajaran</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">KKM</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">Tugas (Avg)</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">Ulangan (Avg)</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">PTS</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">PAS</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">Nilai Akhir</th>
                        <th style="text-align: center; padding: 12px; font-weight: 600; color: #475569;">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch Subjects via Teaching Assignments for this class
                    $class_id = $enrollment['class_id'];
                    $enrollment_id = $enrollment['enrollment_id'];

                    $sqlSubjects = "SELECT ta.id as assignment_id, sub.name as subject_name, ta.kkm 
                                    FROM teaching_assignments ta
                                    JOIN subjects sub ON ta.subject_id = sub.id
                                    WHERE ta.class_id = ?
                                    ORDER BY sub.name ASC";
                    $stmtSub = $pdo->prepare($sqlSubjects);
                    $stmtSub->execute([$class_id]);
                    $assignments = $stmtSub->fetchAll();

                    foreach($assignments as $asg):
                        // Calculate Grades for each assignment
                        // Average Tugas
                        $sql = "SELECT AVG(sg.score) FROM student_grades sg 
                                JOIN assessments a ON sg.assessment_id = a.id
                                WHERE a.teaching_assignment_id = ? AND sg.student_enrollment_id = ? AND a.type = 'Tugas'";
                        $avgTugas = $pdo->prepare($sql);
                        $avgTugas->execute([$asg['assignment_id'], $enrollment_id]);
                        $valTugas = $avgTugas->fetchColumn();

                        // Average UH
                        $sql = "SELECT AVG(sg.score) FROM student_grades sg 
                                JOIN assessments a ON sg.assessment_id = a.id
                                WHERE a.teaching_assignment_id = ? AND sg.student_enrollment_id = ? AND a.type = 'UH'";
                        $avgUH = $pdo->prepare($sql);
                        $avgUH->execute([$asg['assignment_id'], $enrollment_id]);
                        $valUH = $avgUH->fetchColumn();

                        // PTS
                        $sql = "SELECT sg.score FROM student_grades sg 
                                JOIN assessments a ON sg.assessment_id = a.id
                                WHERE a.teaching_assignment_id = ? AND sg.student_enrollment_id = ? AND a.type = 'PTS' LIMIT 1";
                        $pts = $pdo->prepare($sql);
                        $pts->execute([$asg['assignment_id'], $enrollment_id]);
                        $valPTS = $pts->fetchColumn();

                        // PAS
                        $sql = "SELECT sg.score FROM student_grades sg 
                                JOIN assessments a ON sg.assessment_id = a.id
                                WHERE a.teaching_assignment_id = ? AND sg.student_enrollment_id = ? AND a.type = 'PAS' LIMIT 1";
                        $pas = $pdo->prepare($sql);
                        $pas->execute([$asg['assignment_id'], $enrollment_id]);
                        $valPAS = $pas->fetchColumn();

                        // Naive Final Score calc (can be adjusted): 30% Tugas + 20% UH + 20% PTS + 30% PAS
                        $final = ($valTugas * 0.3) + ($valUH * 0.2) + ($valPTS * 0.2) + ($valPAS * 0.3);
                        
                        // Predikat
                        $p = 'D';
                        if ($final >= 90) $p = 'A';
                        elseif ($final >= 80) $p = 'B';
                        elseif ($final >= 70) $p = 'C';
                    ?>
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars($asg['subject_name']); ?></td>
                        <td style="padding: 12px; text-align: center; color: #64748B;"><?php echo $asg['kkm']; ?></td>
                        <td style="padding: 12px; text-align: center;"><?php echo $valTugas ? number_format($valTugas, 1) : '-'; ?></td>
                        <td style="padding: 12px; text-align: center;"><?php echo $valUH ? number_format($valUH, 1) : '-'; ?></td>
                        <td style="padding: 12px; text-align: center;"><?php echo $valPTS ? number_format($valPTS, 1) : '-'; ?></td>
                        <td style="padding: 12px; text-align: center;"><?php echo $valPAS ? number_format($valPAS, 1) : '-'; ?></td>
                        <td style="padding: 12px; text-align: center; font-weight: 700; color: var(--primary);">
                            <?php echo $final > 0 ? number_format($final, 1) : '-'; ?>
                        </td>
                         <td style="padding: 12px; text-align: center;">
                            <?php if ($final > 0): ?>
                            <span style="
                                background: <?php echo $p == 'A' || $p == 'B' ? '#F0FDF4' : ($p == 'C' ? '#FFF7ED' : '#FEF2F2'); ?>; 
                                color: <?php echo $p == 'A' || $p == 'B' ? '#16A34A' : ($p == 'C' ? '#EA580C' : '#DC2626'); ?>; 
                                padding: 2px 10px; border-radius: 99px; font-weight: 600; font-size: 0.85rem;">
                                <?php echo $p; ?>
                            </span>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px; background: #F8FAFC; border-radius: 12px; border: 1px dashed #CBD5E1;">
            <i class="ph ph-warning-circle" style="font-size: 2rem; color: #94A3B8; margin-bottom: 10px;"></i>
            <p style="color: #64748B;">Anda belum terdaftar dalam kelas aktif manapun.<br>Nilai tidak dapat ditampilkan.</p>
        </div>
    <?php endif; ?>
</div>
