<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipProgram;

/**
 * SakipProgramSearch represents the model behind the search form of `backend\models\SakipProgram`.
 */
class SakipProgramSearch extends SakipProgram
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refprogram_id', 'refurusan_id', 'refbidang_id'], 'integer'],
            [['kode_program', 'nama_program', 'program_isaktif'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipProgram::find();

        // add conditions that should always apply here
        $query->with(['urusan', 'bidang']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refprogram_id' => $this->refprogram_id,
            'refurusan_id' => $this->refurusan_id,
            'refbidang_id' => $this->refbidang_id,
        ]);

        $query->andFilterWhere(['like', 'kode_program', $this->kode_program])
            ->andFilterWhere(['like', 'nama_program', $this->nama_program])
            ->andFilterWhere(['like', 'program_isaktif', $this->program_isaktif]);

        return $dataProvider;
    }
}
