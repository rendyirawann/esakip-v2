<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipRekening;

/**
 * SakipRekeningSearch represents the model behind the search form of `backend\models\SakipRekening`.
 */
class SakipRekeningSearch extends SakipRekening
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refrekening_id'], 'integer'],
            [['kode_rekening', 'nama_rekening', 'rekening_isaktif'], 'safe'],
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
        $query = SakipRekening::find();

        // add conditions that should always apply here

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
            'refrekening_id' => $this->refrekening_id,
        ]);

        $query->andFilterWhere(['like', 'kode_rekening', $this->kode_rekening])
            ->andFilterWhere(['like', 'nama_rekening', $this->nama_rekening])
            ->andFilterWhere(['like', 'rekening_isaktif', $this->rekening_isaktif]);

        return $dataProvider;
    }
}
