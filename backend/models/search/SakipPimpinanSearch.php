<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipPimpinan;

/**
 * SakipPimpinanSearch represents the model behind the search form of `backend\models\SakipPimpinan`.
 */
class SakipPimpinanSearch extends SakipPimpinan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpimpinan_id', 'refperiode_id'], 'integer'],
            [['nama_pimpinan', 'jabatan_pimpinan', 'nama_wpimpinan', 'jabatan_wpimpinan', 'user_edit', 'date_edit'], 'safe'],
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
        $query = SakipPimpinan::find();

        // add conditions that should always apply here
        $query->with(['refPeriode']);

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
            'refpimpinan_id' => $this->refpimpinan_id,
            'refperiode_id' => $this->refperiode_id,
            'date_edit' => $this->date_edit,
        ]);

        $query->andFilterWhere(['like', 'nama_pimpinan', $this->nama_pimpinan])
            ->andFilterWhere(['like', 'jabatan_pimpinan', $this->jabatan_pimpinan])
            ->andFilterWhere(['like', 'nama_wpimpinan', $this->nama_wpimpinan])
            ->andFilterWhere(['like', 'jabatan_wpimpinan', $this->jabatan_wpimpinan])
            ->andFilterWhere(['like', 'user_edit', $this->user_edit]);

        return $dataProvider;
    }
}
